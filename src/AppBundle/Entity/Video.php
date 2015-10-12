<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Raphy\Epitech\UserBundle\Entity\User;

/**
 * Video
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Video
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="youtube", type="array")
     */
    private $youtube;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Raphy\Epitech\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_login", referencedColumnName="login", nullable=false)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

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
     * Set user
     *
     * @param \Raphy\Epitech\UserBundle\Entity\User $user
     *
     * @return Video
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

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Video
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set youtube
     *
     * @param array $youtube
     *
     * @return Video
     */
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;

        return $this;
    }

    /**
     * Get youtube
     *
     * @return array
     */
    public function getYoutube()
    {
        return $this->youtube;
    }
}
