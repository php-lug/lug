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

use Lug\Component\Assetic\Rewriter\CssRewriter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = $this->createTreeBuilder();

        $children = $treeBuilder->root('lug_assetic')->children();
        $children->append($this->createFiltersNode());

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createFiltersNode()
    {
        $filtersNode = $this->createNode('filters')->addDefaultsIfNotSet();

        $children = $filtersNode->children();
        $children->append($this->createCssRewriteFilterNode());

        return $filtersNode;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createCssRewriteFilterNode()
    {
        $cssRewriteFilterNode = $this->createNode('css_rewrite')->canBeEnabled();

        $children = $cssRewriteFilterNode->children();
        $children->scalarNode('web_directory')
            ->validate()
                ->ifTrue(function ($webDirectory) {
                    return !file_exists($webDirectory);
                })
                ->thenInvalid('The css rewrite web directory %s does not exist.')
            ->end()
            ->defaultValue('%kernel.root_dir%/../web');

        $children->scalarNode('rewrite_directory')->defaultValue('/assets');
        $children->scalarNode('namer')->defaultValue('lug.assetic.namer.md5');
        $children->scalarNode('strategy')
            ->validate()
                ->ifNotInArray([CssRewriter::STRATEGY_COPY, CssRewriter::STRATEGY_SYMLINK])
                ->thenInvalid('The css rewrite strategy %s is not supported.')
            ->end()
            ->defaultValue(CssRewriter::STRATEGY_COPY);

        return $cssRewriteFilterNode;
    }

    /**
     * @param string           $name
     * @param string           $type
     * @param null|NodeBuilder $builder
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function createNode($name, $type = 'array', NodeBuilder $builder = null)
    {
        return $this->createTreeBuilder()->root($name, $type, $builder);
    }

    /**
     * @return TreeBuilder
     */
    private function createTreeBuilder()
    {
        return new TreeBuilder();
    }
}
