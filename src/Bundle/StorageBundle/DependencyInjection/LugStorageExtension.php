<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\StorageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugStorageExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadCookie($config['cookie'], $container, $loader);
        $this->loadDoctrine($config['doctrine'], $container, $loader);
        $this->loadSession($config['session'], $container, $loader);
    }

    /**
     * @param mixed[]          $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function loadCookie(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (!$config['enabled']) {
            return;
        }

        $loader->load('cookie.xml');
    }

    /**
     * @param mixed[]          $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function loadDoctrine(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (!$config['enabled']) {
            return;
        }

        $loader->load('doctrine.xml');
        $container->getDefinition('lug.storage.doctrine')->addArgument(new Reference($config['service']));
    }

    /**
     * @param mixed[]          $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function loadSession(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (!$config['enabled']) {
            return;
        }

        $loader->load('session.xml');
    }
}
