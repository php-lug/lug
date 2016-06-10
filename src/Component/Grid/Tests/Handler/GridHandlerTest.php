<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Handler;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Filter\FiltererInterface;
use Lug\Component\Grid\Handler\GridHandler;
use Lug\Component\Grid\Handler\GridHandlerInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Slicer\SlicerInterface;
use Lug\Component\Grid\Sort\SorterInterface;
use Lug\Component\Grid\View\GridViewFactoryInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridHandler
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|GridViewFactoryInterface
     */
    private $gridViewFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FiltererInterface
     */
    private $filterer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SorterInterface
     */
    private $sorter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SlicerInterface
     */
    private $slicer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->repositoryRegistry = $this->createServiceRegistryMock();
        $this->gridViewFactory = $this->createGridViewFactoryMock();
        $this->filterer = $this->createFiltererMock();
        $this->sorter = $this->createSorterMock();
        $this->slicer = $this->createSlicerMock();

        $this->handler = new GridHandler(
            $this->repositoryRegistry,
            $this->gridViewFactory,
            $this->filterer,
            $this->sorter,
            $this->slicer
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridHandlerInterface::class, $this->handler);
    }

    public function testHandle()
    {
        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $grid
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options = ['foo' => 'bar']));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->repositoryRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($repository = $this->createRepositoryMock()));

        $repository
            ->expects($this->once())
            ->method('createDataSourceBuilder')
            ->with($this->identicalTo($options))
            ->will($this->returnValue($builder = $this->createDataSourceBuilderMock()));

        $this->filterer
            ->expects($this->once())
            ->method('filter')
            ->with(
                $this->identicalTo($builder),
                $this->identicalTo($grid),
                $this->identicalTo($filters = ['filter'])
            );

        $this->sorter
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo($builder),
                $this->identicalTo($grid),
                $this->identicalTo($sorting = ['sort'])
            );

        $this->slicer
            ->expects($this->once())
            ->method('slice')
            ->with(
                $this->identicalTo($builder),
                $this->identicalTo($grid),
                $this->identicalTo($slicing = ['slice'])
            );

        $this->gridViewFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($grid),
                $this->identicalTo($builder)
            )
            ->will($this->returnValue($view = $this->createGridViewMock()));

        $this->assertSame($view, $this->handler->handle($grid, $filters, $sorting, $slicing));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->createMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewFactoryInterface
     */
    private function createGridViewFactoryMock()
    {
        return $this->createMock(GridViewFactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FiltererInterface
     */
    private function createFiltererMock()
    {
        return $this->createMock(FiltererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SorterInterface
     */
    private function createSorterMock()
    {
        return $this->createMock(SorterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SlicerInterface
     */
    private function createSlicerMock()
    {
        return $this->createMock(SlicerInterface::class);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->createMock(RepositoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }
}
