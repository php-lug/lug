<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\DependencyInjection\Compiler;

use Lug\Bundle\GridBundle\Context\LocaleContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ReplaceLocaleContextPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('lug.locale.context')) {
            return;
        }

        $container
            ->getDefinition('lug.grid.context.locale')
            ->setClass(LocaleContext::class)
            ->replaceArgument(0, new Reference('lug.locale.context'));
    }
}
