<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Lug\Component\Translation\Factory\TranslatableFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigureFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('lug.factory') as $service => $attributes) {
            $factory = $container->getDefinition($service);

            if (!is_a($factory->getClass(), TranslatableFactory::class, true)) {
                continue;
            }

            $factory
                ->addArgument(new Reference('lug.translation.context.locale'))
                ->addArgument($this->getTranslationFactory($attributes[0]['resource'], $container));
        }
    }

    /**
     * @param string           $resource
     * @param ContainerBuilder $container
     *
     * @return Reference
     */
    private function getTranslationFactory($resource, ContainerBuilder $container)
    {
        $translation = null;

        foreach ($container->getDefinition('lug.resource.'.$resource)->getMethodCalls() as $methodCall) {
            if ($methodCall[0] === 'addRelation' && $methodCall[1][0] === 'translation') {
                $translation = $container->getDefinition((string) $methodCall[1][1])->getArgument(0);
            }
        }

        return new Reference('lug.factory.'.$translation);
    }
}
