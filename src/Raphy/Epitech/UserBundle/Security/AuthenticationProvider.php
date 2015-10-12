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
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class AuthenticationProvider
 *
 * @author Raphael De Freitas <raphael@de-freitas.net>
 */
class AuthenticationProvider extends DaoAuthenticationProvider
{
    private $entityManager;

    private $superAdminLogins;

    public function __construct(EntityManager $entityManager, UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, EncoderFactoryInterface $encoderFactory, $hideUserNotFoundExceptions = true, array $superAdminLogins = array())
    {
        parent::__construct($userProvider, $userChecker, $providerKey, $encoderFactory, $hideUserNotFoundExceptions);
        $this->entityManager = $entityManager;
        $this->superAdminLogins = $superAdminLogins;
    }

    public function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        $connector = new \EpitechAPI\Connector();
        $connector->authenticate(\EpitechAPI\Connector::SIGN_IN_METHOD_CREDENTIALS, $user->getUsername(), $token->getCredentials());
        if (!$connector->isSignedIn())
            throw new BadCredentialsException();
        $user->updateFromIntranet($connector->getUser());
        $user->setLastConnectionDate(new \DateTime());
        $roles = $user->getRoles();
        foreach (array_keys($roles, "ROLE_SUPER_ADMIN") as $key)
            unset($roles[$key]);
        if (in_array($user->getLogin(), $this->superAdminLogins))
            $roles[] = "ROLE_SUPER_ADMIN";
        $user->setRoles($roles);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}