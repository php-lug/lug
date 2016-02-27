<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\DataSource;

use Lug\Component\Grid\DataSource\ArrayDataSource;
use Lug\Component\Grid\DataSource\DataSourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ArrayDataSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayDataSource
     */
    private $dataSource;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->dataSource = new ArrayDataSource();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(DataSourceInterface::class, $this->dataSource);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->dataSource));
        $this->assertCount(0, $this->dataSource);
    }

    public function testInitialState()
    {
        $this->dataSource = new ArrayDataSource($objects = [new \stdClass()]);

        $this->assertSame($objects, iterator_to_array($this->dataSource));
        $this->assertCount(count($objects), $this->dataSource);
    }
}
