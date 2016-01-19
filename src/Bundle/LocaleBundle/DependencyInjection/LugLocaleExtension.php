<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\DependencyInjection;

use Lug\Bundle\ResourceBundle\DependencyInjection\Extension\ResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugLocaleExtension extends ResourceExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadBundle(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (['context', 'event_subscriber', 'form', 'negotiator', 'provider', 'validator'] as $resource) {
            $loader->load($resource.'.xml');
        }

        $this->loadForm($container);
        $this->loadProvider($config, $container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadForm(ContainerBuilder $container)
    {
        $container->getDefinition('lug.form.type.locale')->addArgument(new Reference('lug.locale.provider'));
    }

    /**
     * @param mixed[]          $config
     * @param ContainerBuilder $container
     */
    private function loadProvider(array $config, ContainerBuilder $container)
    {
        $container->setParameter('lug.locale', $config['default_locale']);
        $container->getDefinition('lug.locale.provider')->addArgument($config['default_locale']);
    }
}
