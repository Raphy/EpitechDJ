<?php

namespace Raphy\Epitech\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="login", type="string", length=50)
     * @ORM\Id
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = array('ROLE_USER');


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_conection_date", type="datetime")
     */
    private $lastConnectionDate;

    public function __construct()
    {
        $this->lastConnectionDate = new \DateTime();
    }

    public function updateFromIntranet(\EpitechAPI\Component\User $intranetUser)
    {
        $this->login = $intranetUser->getLogin();
        $this->firstName = $intranetUser->getFirstName();
        $this->lastName = $intranetUser->getLastName();
        $this->roles = array("ROLE_USER");
        foreach ($intranetUser->getGroupsName() as $groupName) {
            $this->roles[] = "ROLE_" . strtoupper($groupName);
        }
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getLogin();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        return null;
    }


    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Set lastConnectionDate
     *
     * @param \DateTime $lastConnectionDate
     *
     * @return User
     */
    public function setLastConnectionDate(\DateTime $lastConnectionDate)
    {
        $this->lastConnectionDate = $lastConnectionDate;

        return $this;
    }

    /**
     * Get lastConnectionDate
     *
     * @return \DateTime
     */
    public function getLastConnectionDate()
    {
        return $this->lastConnectionDate;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFullName()
    {
        return ucfirst($this->getFirstName()) . " " . ucfirst($this->getLastName());
    }
}
