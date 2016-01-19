<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Sort;

use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface SorterRendererInterface
{
    /**
     * @param GridViewInterface $grid
     * @param ColumnInterface   $column
     * @param string            $sorting
     *
     * @return string|null
     */
    public function render(GridViewInterface $grid, ColumnInterface $column, $sorting);
}
