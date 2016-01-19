<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Handler;

use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface GridHandlerInterface
{
    /**
     * @param GridInterface $grid
     * @param mixed[]       $filters
     * @param string[]      $sorting
     * @param mixed[]       $slicing
     *
     * @return GridViewInterface
     */
    public function handle(GridInterface $grid, array $filters = [], array $sorting = [], array $slicing = []);
}
