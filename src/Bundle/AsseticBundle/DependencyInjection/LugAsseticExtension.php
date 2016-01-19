<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AsseticBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugAsseticExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadFilters($config['filters'], $container, $loader);
    }

    /**
     * @param mixed[]          $filters
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function loadFilters(array $filters, ContainerBuilder $container, LoaderInterface $loader)
    {
        if ($filters['css_rewrite']['enabled']) {
            $this->loadCssRewriteFilter($filters['css_rewrite'], $container, $loader);
        }
    }

    /**
     * @param string[]         $cssRewrite
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function loadCssRewriteFilter(array $cssRewrite, ContainerBuilder $container, LoaderInterface $loader)
    {
        foreach (['filter', 'namer', 'rewriter'] as $resource) {
            $loader->load($resource.'.xml');
        }

        $cssRewriter = $container->getDefinition('lug.assetic.rewriter.css');

        $cssRewriter->addArgument(new Reference($cssRewrite['namer']));
        $cssRewriter->addArgument($cssRewrite['web_directory']);
        $cssRewriter->addArgument($cssRewrite['rewrite_directory']);
        $cssRewriter->addArgument($cssRewrite['strategy']);
    }
}
