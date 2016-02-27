<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\DataSource;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ArrayDataSource implements DataSourceInterface
{
    /**
     * @var object[]
     */
    private $array;

    /**
     * @param object[] $array
     */
    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->array);
    }
}
