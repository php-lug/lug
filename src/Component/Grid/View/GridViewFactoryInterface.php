<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\View;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface GridViewFactoryInterface
{
    /**
     * @param GridInterface              $definition
     * @param DataSourceBuilderInterface $dataSourceBuilder
     *
     * @return GridViewInterface
     */
    public function create(GridInterface $definition, DataSourceBuilderInterface $dataSourceBuilder);
}
