<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\RegistryBundle\DependencyInjection\Compiler;

use Lug\Bundle\RegistryBundle\Model\LazyRegistry;
use Lug\Bundle\RegistryBundle\Model\LazyRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConvertRegistryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (array_keys($container->findTaggedServiceIds($tag = 'lug.registry')) as $registry) {
            $definition = $container->getDefinition($registry);

            if (is_subclass_of($definition->getClass(), LazyRegistryInterface::class)) {
                continue;
            }

            $container->setDefinition($internal = $registry.'.internal', $definition->clearTag($tag));
            $container->setDefinition($registry, $this->createLazyDefinition($internal, $definition));

            $this->clearMethodCalls($definition);
        }
    }

    /**
     * @param string     $registry
     * @param Definition $definition
     *
     * @return Definition
     */
    private function createLazyDefinition($registry, Definition $definition)
    {
        $lazy = new Definition(LazyRegistry::class, [
            new Reference('service_container'),
            new Reference($registry),
        ]);

        foreach ($definition->getMethodCalls() as $methodCall) {
            if ($methodCall[0] === 'offsetSet') {
                $methodCall[1][1] = (string) $methodCall[1][1];
                $lazy->addMethodCall('setLazy', $methodCall[1]);
            }
        }

        return $lazy;
    }

    /**
     * @param Definition $definition
     */
    private function clearMethodCalls(Definition $definition)
    {
        $methodCall = 'offsetSet';

        while ($definition->hasMethodCall($methodCall)) {
            $definition->removeMethodCall($methodCall);
        }
    }
}
