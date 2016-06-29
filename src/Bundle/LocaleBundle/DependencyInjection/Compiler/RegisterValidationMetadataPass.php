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
use Symfony\Component\DependencyInjection\Definition;

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
        $driver = $this->getLocaleDriver($container->getDefinition('lug.resource.locale'));

        if ($driver === null) {
            return;
        }

        $builder = $container->getDefinition('validator.builder');
        $path = realpath(__DIR__.'/../../Resources/config/validator');

        if ($driver === ResourceInterface::DRIVER_DOCTRINE_ORM) {
            $builder->addMethodCall('addXmlMapping', [$path.'/Locale.orm.xml']);
        } elseif ($driver === ResourceInterface::DRIVER_DOCTRINE_MONGODB) {
            $builder->addMethodCall('addXmlMapping', [$path.'/Locale.mongodb.xml']);
        }
    }

    /**
     * @param Definition $definition
     *
     * @return string|null
     */
    private function getLocaleDriver(Definition $definition)
    {
        foreach ($definition->getMethodCalls() as $methodCall) {
            if ($methodCall[0] === 'setDriver') {
                return $methodCall[1][0];
            }
        }
    }
}
