<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Video;
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
                "message" => "La vidéo Youtube n'existe pas."
            ), 200);

        if ($videoYoutubeData["length_seconds"] > 360) {
            return new JsonResponse(array(
                "type" => "alert",
                "message" => "La vidéo Youtube dépasse les 6 min"
            ), 200);
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
