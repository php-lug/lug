<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\DependencyInjection\Compiler;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterValidationMetadataPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $builder = $container->getDefinition('validator.builder');
        $driver = $container->getDefinition('lug.resource.locale')->getArgument(1);
        $path = realpath(__DIR__.'/../../Resources/config/validator');

        if ($driver === ResourceInterface::DRIVER_DOCTRINE_ORM) {
            $builder->addMethodCall('addXmlMapping', [$path.'/Locale.orm.xml']);
        } elseif ($driver === ResourceInterface::DRIVER_DOCTRINE_MONGODB) {
            $builder->addMethodCall('addXmlMapping', [$path.'/Locale.mongodb.xml']);
        }
    }
}
