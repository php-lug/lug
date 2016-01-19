<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Renderer;

use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RendererInterface
{
    /**
     * @param GridViewInterface $grid
     *
     * @return string
     */
    public function render(GridViewInterface $grid);

    /**
     * @param GridViewInterface $grid
     *
     * @return string
     */
    public function renderFilters(GridViewInterface $grid);

    /**
     * @param GridViewInterface $grid
     *
     * @return string
     */
    public function renderGrid(GridViewInterface $grid);

    /**
     * @param GridViewInterface $grid
     * @param ColumnInterface   $column
     * @param mixed             $data
     *
     * @return string
     */
    public function renderColumn(GridViewInterface $grid, ColumnInterface $column, $data);

    /**
     * @param GridViewInterface $grid
     * @param ColumnInterface   $column
     *
     * @return string
     */
    public function renderColumnSortings(GridViewInterface $grid, ColumnInterface $column);

    /**
     * @param GridViewInterface $grid
     * @param ColumnInterface   $column
     * @param string            $sorting
     *
     * @return string
     */
    public function renderColumnSorting(GridViewInterface $grid, ColumnInterface $column, $sorting);

    /**
     * @param GridViewInterface $grid
     * @param mixed             $data
     *
     * @return string
     */
    public function renderColumnActions(GridViewInterface $grid, $data);

    /**
     * @param GridViewInterface $grid
     * @param ActionInterface   $action
     * @param mixed             $data
     *
     * @return string
     */
    public function renderColumnAction(GridViewInterface $grid, ActionInterface $action, $data);

    /**
     * @param GridViewInterface $grid
     *
     * @return string
     */
    public function renderGlobalActions(GridViewInterface $grid);

    /**
     * @param GridViewInterface $grid
     * @param ActionInterface   $action
     *
     * @return string
     */
    public function renderGlobalAction(GridViewInterface $grid, ActionInterface $action);
}
