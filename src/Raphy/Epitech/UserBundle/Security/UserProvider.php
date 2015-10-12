<?php

/*
 * This file is part of the EpitechYoutubeDJ package.
 *
 * (c) Raphael De Freitas <raphael@de-freitas.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raphy\Epitech\UserBundle\Security;

use Doctrine\ORM\EntityManager;
use Raphy\Epitech\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 *
 * @author Raphael De Freitas <raphael@de-freitas.net>
 */
class UserProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        if (empty($username))
            throw new UsernameNotFoundException();
        if ($user = $this->findUserBy(array("login" => $username)))
            return $user;
        $user = new User();
        $user->setLogin($username);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $class === "Raphy\\Epitech\\UserBundle\\Entity\\User";
    }

    protected function findUserBy(array $criteria)
    {
        $repository = $this->entityManager->getRepository("Raphy\\Epitech\\UserBundle\\Entity\\User");
        return $repository->findOneBy($criteria);
    }
}