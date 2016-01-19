<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Filter;

use Lug\Component\Grid\Model\GridInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FilterManagerInterface
{
    /**
     * @param GridInterface $grid
     *
     * @return mixed[]
     */
    public function get(GridInterface $grid);

    /**
     * @param GridInterface $grid
     * @param mixed[]       $filters
     */
    public function set(GridInterface $grid, array $filters);
}
