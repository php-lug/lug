<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Model;

use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface GridInterface
{
    /**
     * @return ResourceInterface
     */
    public function getResource();

    /**
     * @return bool
     */
    public function hasColumns();

    /**
     * @return ColumnInterface[]
     */
    public function getColumns();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn($name);

    /**
     * @param string $name
     *
     * @return ColumnInterface
     */
    public function getColumn($name);

    /**
     * @return bool
     */
    public function hasFilters();

    /**
     * @return FilterInterface[]
     */
    public function getFilters();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFilter($name);

    /**
     * @param string $name
     *
     * @return FilterInterface
     */
    public function getFilter($name);

    /**
     * @return bool
     */
    public function hasSorts();

    /**
     * @return SortInterface[]
     */
    public function getSorts();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasSort($name);

    /**
     * @param string $name
     *
     * @return SortInterface
     */
    public function getSort($name);

    /**
     * @return bool
     */
    public function hasGlobalActions();

    /**
     * @return ActionInterface[]
     */
    public function getGlobalActions();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasGlobalAction($name);

    /**
     * @param string $name
     *
     * @return ActionInterface
     */
    public function getGlobalAction($name);

    /**
     * @return bool
     */
    public function hasColumnActions();

    /**
     * @return ActionInterface[]
     */
    public function getColumnActions();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasColumnAction($name);

    /**
     * @param string $name
     *
     * @return ActionInterface
     */
    public function getColumnAction($name);

    /**
     * @return bool
     */
    public function hasBatches();

    /**
     * @return BatchInterface[]
     */
    public function getBatches();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasBatch($name);

    /**
     * @param string $name
     *
     * @return BatchInterface
     */
    public function getBatch($name);

    /**
     * @return bool
     */
    public function hasData();

    /**
     * @return mixed[]
     */
    public function getData();

    /**
     * @return bool
     */
    public function hasOptions();

    /**
     * @return mixed[]
     */
    public function getOptions();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOption($name);
}
