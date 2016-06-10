<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Filter;

use Lug\Bundle\GridBundle\Filter\FilterManager;
use Lug\Bundle\GridBundle\Filter\FilterManagerInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Storage\Model\StorageInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterManager
     */
    private $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StorageInterface
     */
    private $storage;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->storage = $this->createStorageMock();
        $this->manager = new FilterManager($this->storage);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FilterManagerInterface::class, $this->manager);
    }

    public function testGet()
    {
        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->storage
            ->expects($this->once())
            ->method('offsetExists')
            ->with($this->identicalTo($key = '_lug_grid_filter_'.$name))
            ->will($this->returnValue(true));

        $this->storage
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($key))
            ->will($this->returnValue($data = ['foo' => 'bar']));

        $this->assertSame($data, $this->manager->get($grid));
    }

    public function testGetWithoutCache()
    {
        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->storage
            ->expects($this->once())
            ->method('offsetExists')
            ->with($this->identicalTo('_lug_grid_filter_'.$name))
            ->will($this->returnValue(false));

        $grid
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($filters = ['foo' => 'bar']));

        $this->assertSame($filters, $this->manager->get($grid));
    }

    public function testGetWithoutStorage()
    {
        $this->manager = new FilterManager();

        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($filters = ['foo' => 'bar']));

        $this->assertSame($filters, $this->manager->get($grid));
    }

    public function testSet()
    {
        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->storage
            ->expects($this->once())
            ->method('offsetSet')
            ->with(
                $this->identicalTo($key = '_lug_grid_filter_'.$name),
                $this->identicalTo($filters = ['foo' => 'bar'])
            );

        $this->manager->set($grid, $filters);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StorageInterface
     */
    private function createStorageMock()
    {
        return $this->createMock(StorageInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
