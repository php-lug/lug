<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (['controller', 'menu'] as $resource) {
            $loader->load($resource.'.xml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['LugGridBundle'])) {
            $resources = [
                'body',
                'column_action',
                'column_sorting',
                'column_sortings',
                'filters',
                'global_action',
            ];

            $templates = [];

            foreach ($resources as $resource) {
                $templates[$resource] = '@LugAdmin/Grid/'.$resource.'.html.twig';
            }

            $container->prependExtensionConfig('lug_grid', ['templates' => $templates]);
        }
    }
}
