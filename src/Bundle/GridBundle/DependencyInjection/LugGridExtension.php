<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugGridExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $resources = [
            'action',
            'batch',
            'builder',
            'column',
            'context',
            'filter',
            'form',
            'handler',
            'registry',
            'renderer',
            'slicer',
            'sort',
            'twig',
            'view',
        ];

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($resources as $resource) {
            $loader->load($resource.'.xml');
        }

        $this->loadTemplates($config['templates'], $container);
        $this->loadColumns($config['columns'], $container);
    }

    /**
     * @param string[]         $templates
     * @param ContainerBuilder $container
     */
    private function loadTemplates(array $templates, ContainerBuilder $container)
    {
        $container->getDefinition('lug.grid.renderer')->addArgument($templates);
    }

    /**
     * @param array            $columns
     * @param ContainerBuilder $container
     */
    private function loadColumns(array $columns, ContainerBuilder $container)
    {
        $container->getDefinition('lug.grid.column.type.boolean')->addArgument($columns['boolean']['template']);
    }
}
