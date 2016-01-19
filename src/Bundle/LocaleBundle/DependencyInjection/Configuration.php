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

use Lug\Bundle\ResourceBundle\DependencyInjection\Extension\ResourceConfiguration;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Configuration extends ResourceConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function configureBundle(NodeBuilder $builder)
    {
        $builder->scalarNode('default_locale')->defaultValue('%locale%');
    }
}
