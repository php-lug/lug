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
use Lug\Component\Storage\Model\StorageInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterManager implements FilterManagerInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function get(GridInterface $grid)
    {
        return isset($this->storage[$key = $this->getKey($grid)])
            ? $this->storage[$key]
            : $grid->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function set(GridInterface $grid, array $filters)
    {
        $this->storage[$this->getKey($grid)] = $filters;
    }

    /**
     * @param GridInterface $grid
     *
     * @return string
     */
    private function getKey(GridInterface $grid)
    {
        return '_lug_grid_filter_'.$grid->getResource()->getName();
    }
}
