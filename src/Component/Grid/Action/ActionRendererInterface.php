<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Action;

use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ActionRendererInterface
{
    /**
     * @param GridViewInterface $grid
     * @param ActionInterface   $action
     * @param mixed             $data
     *
     * @return string
     */
    public function render(GridViewInterface $grid, ActionInterface $action, $data);
}
