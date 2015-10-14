<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raphy\Epitech\UserBundle\Entity\User;

/**
 * Vote
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Vote
{
    const VOTE_TYPE_VOLUME_UP = "volume_up";
    const VOTE_TYPE_VOLUME_DOWN = "volume_down";
    const VOTE_TYPE_LIKE = "like";
    const VOTE_TYPE_DISLIKE = "dislike";
    const VOTE_TYPE_REPEAT = "repeat";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Raphy\Epitech\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_login", referencedColumnName="login", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Vote
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \Raphy\Epitech\UserBundle\Entity\User $user
     *
     * @return Vote
     */
    public function setUser(\Raphy\Epitech\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Raphy\Epitech\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
