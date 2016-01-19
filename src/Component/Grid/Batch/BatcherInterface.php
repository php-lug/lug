<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Batch;

use Lug\Component\Grid\Model\GridInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface BatcherInterface
{
    /**
     * @param GridInterface $grid
     * @param string        $batch
     * @param mixed         $data
     */
    public function batch(GridInterface $grid, $batch, $data);
}
