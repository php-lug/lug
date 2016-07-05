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

            if (is_a($factory->getClass(), TranslatableFactory::class, true)) {
                $factory->addArgument(new Reference('lug.translation.context.locale'));
            }
        }
    }
}
