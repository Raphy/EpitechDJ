<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class VoteController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request)
    {
        if (($type = $request->request->get("type", null)) === null)
            return new Response($type, 406);

        $types = array(
            Vote::VOTE_TYPE_DISLIKE,
            Vote::VOTE_TYPE_LIKE,
            Vote::VOTE_TYPE_REPEAT,
            Vote::VOTE_TYPE_VOLUME_DOWN,
            Vote::VOTE_TYPE_VOLUME_UP,
        );

        if (!in_array($type, $types))
            return new Response($type, 406);

        $voteRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Vote");
        $vote = $voteRepository->findOneBy(array("user" => $this->getUser()));
        if ($vote != null)
            return new Response($type, 403);

        $vote = new Vote();
        $vote->setUser($this->getUser());
        $vote->setType($type);
        $this->getDoctrine()->getManager()->persist($vote);
        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }
}
