<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Model\Builder;

use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\Builder\ActionBuilderInterface;
use Lug\Component\Grid\Model\Builder\BatchBuilderInterface;
use Lug\Component\Grid\Model\Builder\ColumnBuilderInterface;
use Lug\Component\Grid\Model\Builder\FilterBuilderInterface;
use Lug\Component\Grid\Model\Builder\GridBuilder;
use Lug\Component\Grid\Model\Builder\GridBuilderInterface;
use Lug\Component\Grid\Model\Builder\SortBuilderInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ColumnBuilderInterface
     */
    private $columnBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FilterBuilderInterface
     */
    private $filterBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SortBuilderInterface
     */
    private $sortBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ActionBuilderInterface
     */
    private $actionBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BatchBuilderInterface
     */
    private $batchBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->columnBuilder = $this->createColumnBuilderMock();
        $this->filterBuilder = $this->createFilterBuilderMock();
        $this->sortBuilder = $this->createSortBuilderMock();
        $this->actionBuilder = $this->createActionBuilderMock();
        $this->batchBuilder = $this->createBatchBuilderMock();

        $this->builder = new GridBuilder(
            $this->columnBuilder,
            $this->filterBuilder,
            $this->sortBuilder,
            $this->actionBuilder,
            $this->batchBuilder
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridBuilderInterface::class, $this->builder);
    }

    public function testBuild()
    {
        $grid = $this->builder->build(['resource' => $resource = $this->createResourceMock()]);

        $this->assertSame($resource, $grid->getResource());
        $this->assertFalse($grid->hasColumns());
        $this->assertFalse($grid->hasFilters());
        $this->assertFalse($grid->hasSorts());
        $this->assertFalse($grid->hasGlobalActions());
        $this->assertFalse($grid->hasColumnActions());
        $this->assertFalse($grid->hasBatches());
        $this->assertFalse($grid->hasData());
        $this->assertFalse($grid->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $this->columnBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->identicalTo(array_merge(
                ['name' => $columnName = 'column_name'],
                $columnDefinition = ['column' => 'definition']
            )))
            ->will($this->returnValue($column = $this->createColumnMock()));

        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($columnName));

        $this->filterBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->identicalTo(array_merge(
                ['name' => $filterName = 'filter_name'],
                $filterDefinition = ['filter' => 'definition']
            )))
            ->will($this->returnValue($filter = $this->createFilterMock()));

        $filter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($filterName));

        $this->sortBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->identicalTo(array_merge(
                ['name' => $sortName = 'sort_name'],
                $sortDefinition = ['sort' => 'definition']
            )))
            ->will($this->returnValue($sort = $this->createSortMock()));

        $sort
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($sortName));

        $this->actionBuilder
            ->expects($this->at(0))
            ->method('build')
            ->with($this->identicalTo(array_merge(
                ['name' => $globalActionName = 'global_action_name'],
                $globalActionDefinition = ['global_action' => 'definition']
            )))
            ->will($this->returnValue($globalAction = $this->createActionMock()));

        $globalAction
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($globalActionName));

        $this->actionBuilder
            ->expects($this->at(1))
            ->method('build')
            ->with($this->identicalTo(array_merge(
                ['name' => $columnActionName = 'column_action_name'],
                $columnActionDefinition = ['column_action' => 'definition']
            )))
            ->will($this->returnValue($columnAction = $this->createActionMock()));

        $columnAction
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($columnActionName));

        $this->batchBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->identicalTo(array_merge(
                ['name' => $batchName = 'batch_name'],
                $batchDefinition = ['batch' => 'definition']
            )))
            ->will($this->returnValue($batch = $this->createBatchMock()));

        $batch
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($batchName));

        $grid = $this->builder->build([
            'resource'       => $resource = $this->createResourceMock(),
            'columns'        => [$columnName => $columnDefinition],
            'filters'        => [$filterName => $filterDefinition],
            'sorts'          => [$sortName => $sortDefinition],
            'global_actions' => [$globalActionName => $globalActionDefinition],
            'column_actions' => [$columnActionName => $columnActionDefinition],
            'batches'        => [$batchName => $batchDefinition],
            'data'           => $data = ['data'],
            'options'        => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($resource, $grid->getResource());
        $this->assertSame([$columnName => $column], $grid->getColumns());
        $this->assertSame([$filterName => $filter], $grid->getFilters());
        $this->assertSame([$sortName => $sort], $grid->getSorts());
        $this->assertSame([$globalActionName => $globalAction], $grid->getGlobalActions());
        $this->assertSame([$columnActionName => $columnAction], $grid->getColumnActions());
        $this->assertSame([$batchName => $batch], $grid->getBatches());
        $this->assertSame($data, $grid->getData());
        $this->assertSame($options, $grid->getOptions());
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The grid config "resource" could not be found.
     */
    public function testBuildWithMissingResource()
    {
        $this->builder->build([]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnBuilderInterface
     */
    private function createColumnBuilderMock()
    {
        return $this->getMock(ColumnBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterBuilderInterface
     */
    private function createFilterBuilderMock()
    {
        return $this->getMock(FilterBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SortBuilderInterface
     */
    private function createSortBuilderMock()
    {
        return $this->getMock(SortBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionBuilderInterface
     */
    private function createActionBuilderMock()
    {
        return $this->getMock(ActionBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|BatchBuilderInterface
     */
    private function createBatchBuilderMock()
    {
        return $this->getMock(BatchBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterInterface
     */
    private function createFilterMock()
    {
        return $this->getMock(FilterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SortInterface
     */
    private function createSortMock()
    {
        return $this->getMock(SortInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->getMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|BatchInterface
     */
    private function createBatchMock()
    {
        return $this->getMock(BatchInterface::class);
    }
}
