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

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AuthenticationFactory
 *
 * @author Raphael De Freitas <raphael@de-freitas.net>
 */
class AuthenticationFactory extends FormLoginFactory
{
    public function getKey()
    {
        return "intranet_login";
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = "raphy_epitech_user.authentication.provider." . $id;
        $container
            ->setDefinition($provider, new DefinitionDecorator("raphy_epitech_user.authentication.provider"))
            ->replaceArgument(1, new Reference($userProviderId))
            ->replaceArgument(3, $id);
        return $provider;
    }
}