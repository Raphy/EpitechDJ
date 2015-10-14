<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Video;
use AppBundle\Entity\Vote;
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
            ->getQuery()->getOneOrNullResult();

        return $video;
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Template
     */
    public function playerAction()
    {
        return array();
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
        $videosQueryBuilder = $videoRepository->createQueryBuilder("v")
            ->andWhere("v.creationDate > :from")->setParameter(":from", $from)
            ->addOrderBy("v.creationDate", "ASC");
        if ($video)
            $videosQueryBuilder->andWhere("v.id != :id")->setParameter(":id", $video->getId());
        $videos = $videosQueryBuilder->getQuery()->getResult();

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
        /**
         * @var $votes Video[]
         * @var $voteRepository EntityRepository
         */

        $video = $video = $this->getCurrentVideo();
        if ($video) {
            $this->getDoctrine()->getManager()->remove($video);
            $voteRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Vote");
            $votes = $voteRepository->findAll();
            foreach ($votes as $vote)
                $this->getDoctrine()->getManager()->remove($vote);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->currentAction();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function votesAction(Request $request)
    {
        /**
         * @var $votes Vote[]
         * @var $vote Vote
         * @var $voteRepository EntityRepository
         */

        $voteRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Vote");
        $votes = $voteRepository->findAll();

        $votesArr = array(
            "count" => count($votes),
            "volume" => 0,
            "heart" => 0,
            "repeat" => 0
        );

        foreach ($votes as $vote) {
            switch ($vote->getType()) {
                case Vote::VOTE_TYPE_DISLIKE:
                    $votesArr["heart"]--;
                    break;
                case Vote::VOTE_TYPE_LIKE:
                    $votesArr["heart"]++;
                    break;
                case Vote::VOTE_TYPE_REPEAT:
                    $votesArr["repeat"]++;
                    break;
                case Vote::VOTE_TYPE_VOLUME_DOWN:
                    $votesArr["volume"]--;
                    break;
                case Vote::VOTE_TYPE_VOLUME_UP:
                    $votesArr["volume"]++;
                    break;
            }
        }

        return new JsonResponse($votesArr);
    }
}
