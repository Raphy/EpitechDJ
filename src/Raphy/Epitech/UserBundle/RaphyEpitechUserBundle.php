<?php

namespace Raphy\Epitech\UserBundle;

use Raphy\Epitech\UserBundle\Security\AuthenticationFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RaphyEpitechUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new AuthenticationFactory());
    }

}
