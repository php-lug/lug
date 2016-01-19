<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column;

use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ColumnRendererInterface
{
    /**
     * @param GridViewInterface $grid
     * @param ColumnInterface   $column
     * @param mixed             $data
     *
     * @return string
     */
    public function render(GridViewInterface $grid, ColumnInterface $column, $data);
}
