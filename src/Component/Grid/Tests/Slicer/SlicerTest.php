<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Slicer;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Slicer\Slicer;
use Lug\Component\Grid\Slicer\SlicerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SlicerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Slicer
     */
    private $slicer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->slicer = new Slicer();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(SlicerInterface::class, $this->slicer);
    }

    public function testSlice()
    {
        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->once())
            ->method('setLimit')
            ->with($this->identicalTo($limit = 20))
            ->will($this->returnSelf());

        $builder
            ->expects($this->once())
            ->method('setPage')
            ->with($this->identicalTo($page = 3));

        $this->slicer->slice($builder, $this->createGridMock(), ['limit' => $limit, 'page' => $page]);
    }

    public function testSliceWithGridOptions()
    {
        $grid = $this->createGridMock();
        $grid
            ->expects($this->exactly(2))
            ->method('hasOption')
            ->will($this->returnValueMap([
                ['limit', true],
                ['page', true],
            ]));

        $grid
            ->expects($this->exactly(2))
            ->method('getOption')
            ->will($this->returnValueMap([
                ['limit', $limit = 20],
                ['page', $page = 3],
            ]));

        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->once())
            ->method('setLimit')
            ->with($this->identicalTo($limit))
            ->will($this->returnSelf());

        $builder
            ->expects($this->once())
            ->method('setPage')
            ->with($this->identicalTo($page));

        $this->slicer->slice($builder, $grid, []);
    }

    public function testSliceWithDefaultOptions()
    {
        $grid = $this->createGridMock();
        $grid
            ->expects($this->exactly(2))
            ->method('hasOption')
            ->will($this->returnValueMap([
                ['limit', false],
                ['page', false],
            ]));

        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->once())
            ->method('setLimit')
            ->with($this->identicalTo(10))
            ->will($this->returnSelf());

        $builder
            ->expects($this->once())
            ->method('setPage')
            ->with($this->identicalTo(1));

        $this->slicer->slice($builder, $grid, []);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->getMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }
}
