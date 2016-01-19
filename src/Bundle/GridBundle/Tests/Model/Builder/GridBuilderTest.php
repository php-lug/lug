<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Model\Builder;

use Lug\Bundle\GridBundle\Model\Builder\GridBuilder;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\Builder\ActionBuilderInterface;
use Lug\Component\Grid\Model\Builder\BatchBuilderInterface;
use Lug\Component\Grid\Model\Builder\ColumnBuilderInterface;
use Lug\Component\Grid\Model\Builder\FilterBuilderInterface;
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
        $this->assertSame(['trans_domain' => 'grids'], $grid->getOptions());
    }

    public function testBuildWithFullOptions()
    {
        $resource = $this->createResourceMock();
        $resource
            ->expects($this->exactly(5))
            ->method('getName')
            ->will($this->returnValue($resourceName = 'resource'));

        $labelPattern = 'lug.'.$resourceName.'.%s.%s';

        $this->columnBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->identicalTo(array_merge(
                [
                    'label' => sprintf($labelPattern, 'column', $columnName = 'column_name'),
                    'name'  => $columnName,
                ],
                $columnDefinition = ['column' => 'definition'],
                ['options' => ['trans_domain' => 'grids']]
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
                [
                    'label' => sprintf($labelPattern, 'filter', $filterName = 'filter_name'),
                    'name'  => $filterName,
                ],
                $filterDefinition = ['filter' => 'definition'],
                ['options' => ['trans_domain' => 'grids']]
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
                $sortDefinition = ['sort' => 'definition'],
                ['options' => ['trans_domain' => 'grids']]
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
                [
                    'label' => sprintf($labelPattern, 'global_action', $globalActionName = 'global_action_name'),
                    'name'  => $globalActionName,
                ],
                $globalActionDefinition = ['global_action' => 'definition'],
                ['options' => ['trans_domain' => 'grids']]
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
                [
                    'label' => sprintf($labelPattern, 'column_action', $columnActionName = 'column_action_name'),
                    'name'  => $columnActionName,
                ],
                $columnActionDefinition = ['column_action' => 'definition'],
                ['options' => ['trans_domain' => 'grids']]
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
                [
                    'label' => sprintf($labelPattern, 'batch', $batchName = 'batch_name'),
                    'name'  => $batchName,
                ],
                $batchDefinition = ['batch' => 'definition'],
                ['options' => ['trans_domain' => 'grids']]
            )))
            ->will($this->returnValue($batch = $this->createBatchMock()));

        $batch
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($batchName));

        $grid = $this->builder->build([
            'resource'       => $resource,
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
        $this->assertSame(array_merge(['trans_domain' => 'grids'], $options), $grid->getOptions());
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
