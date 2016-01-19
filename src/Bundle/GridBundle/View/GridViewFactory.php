<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\View;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\View\GridViewFactoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridViewFactory implements GridViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(GridInterface $definition, DataSourceBuilderInterface $dataSourceBuilder)
    {
        return new GridView($definition, $dataSourceBuilder);
    }
}
