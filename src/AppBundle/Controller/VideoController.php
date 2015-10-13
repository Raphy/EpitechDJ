<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Video;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VideoController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request)
    {
        if (($url = $request->request->get("url", null)) === null)
            return new JsonResponse(array(
                "type" => "alert",
                "message" => "Le lien de la vidéo n'a pas été fourni."
            ), 200);

        if (preg_match('#^https?://\w{0,3}.?youtube+\.\w{2,3}/watch\?v=([\w-]{11})#', $url, $matches) == false)
            return new JsonResponse(array(
                "type" => "alert",
                "message" => "Le lien de la video Youtube n'est pas valide."
            ), 200);

        $content = file_get_contents("http://youtube.com/get_video_info?video_id=" . $matches[1]);
        parse_str($content, $videoYoutubeData);
        if ($videoYoutubeData["status"] == "fail")
            return new JsonResponse(array(
                "type" => "alert",
                "message" => "La vidéo Youtube n'existe pas. Soit parce qu'elle ... n'existe effectivement pas, soit parce que l'exportation n'est pas autorisée par Youtube."
            ), 200);


        if (!$this->get('security.context')->isGranted("ROLE_SUPER_ADMIN")) {
            if ($videoYoutubeData["length_seconds"] > 360) {
                return new JsonResponse(array(
                    "type" => "alert",
                    "message" => "La vidéo Youtube dépasse les 6 min"
                ), 200);
            }

            /**
             * @var $videos Video[]
             * @var $videoRepository EntityRepository
             */

            $videoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Video");
            $videos = $videoRepository->createQueryBuilder("v")
                ->andWhere("v.user = :user")->setParameter(":user", $this->getUser())
                ->addOrderBy("v.creationDate", "ASC")
                ->getQuery()->getResult();
            if (count($videos) > 0)
                return new JsonResponse(array(
                    "type" => "alert",
                    "message" => "Vous avez déjà une vidéo en file d'attente. Veuillez attendre sa lecture avant d'en soumettre une autre."
                ), 200);

            foreach ($videos as $video) {
                $youtube = $video->getYoutube();
                if ($youtube["video_id"] == $matches[1])
                    return new JsonResponse(array(
                        "type" => "alert",
                        "message" => "Cette vidéo est déjà présente dans la liste d'attente. Veuillez attendre sa lecture avant de la soumettre à nouveau."
                    ), 200);
            }
        }

        $video = new Video();
        $video->setUser($this->getUser());
        $video->setYoutube($videoYoutubeData);
        $this->getDoctrine()->getManager()->persist($video);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array(
            "type" => "success",
            "message" => "Votre vidéo Youtube a été ajoutée"
        ), 201);
    }
}
