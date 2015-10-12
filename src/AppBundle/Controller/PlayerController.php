<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Video;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlayerController extends Controller
{
    /**
     * Get the current playing video
     *
     * @return Video
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getCurrentVideo()
    {
        /**
         * @var $video Video
         * @var $videoRepository EntityRepository
         */
        $videoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Video");
        $video = $videoRepository->createQueryBuilder("v")
            ->addOrderBy("v.creationDate", "ASC")
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();

        return $video;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function currentAction()
    {
        $video = $this->getCurrentVideo();

        if ($video === null)
            throw new NotFoundHttpException();

        $videoJson = array(
            "id" => $video->getId(),
            "timestamp" => $video->getCreationDate()->getTimestamp(),
            "youtube" => $video->getYoutube(),
            "user" => array(
                "login" => $video->getUser()->getLogin(),
                "full_name" => $video->getUser()->getFullName()
            )
        );
        return new JsonResponse($videoJson);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function queueAction(Request $request)
    {
        /**
         * @var $videos Video[]
         * @var $videoRepository EntityRepository
         */

        $from = new \DateTime();
        $from->setTimestamp((int)$request->query->get("from", 0));

        $videoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Video");
        $video = $this->getCurrentVideo();
        $videos = $videoRepository->createQueryBuilder("v")
            ->andWhere("v.creationDate > :from")->setParameter(":from", $from)
            ->andWhere("v.id != :id")->setParameter(":id", $video->getId())
            ->addOrderBy("v.creationDate", "ASC")
            ->getQuery()->getResult();

        $videosArr = [];
        foreach ($videos as $video) {
            $videosArr[] = array(
                "id" => $video->getId(),
                "timestamp" => $video->getCreationDate()->getTimestamp(),
                "youtube" => $video->getYoutube(),
                "user" => array(
                    "login" => $video->getUser()->getLogin(),
                    "full_name" => $video->getUser()->getFullName()
                )
            );
        }

        return new JsonResponse($videosArr);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function finishAction()
    {
        $video = $video = $this->getCurrentVideo();

        if ($video) {
            $this->getDoctrine()->getManager()->remove($video);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->currentAction();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function voteAction(Request $request)
    {
        if ($request->getMethod() === "GET") {

        } else {

        }
        $video = $video = $this->getCurrentVideo();

        if ($video) {
            $this->getDoctrine()->getManager()->remove($video);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->currentAction();
    }
}
