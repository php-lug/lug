<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Model;

use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Grid\Model\Grid;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->grid = new Grid($this->resource);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridInterface::class, $this->grid);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->resource, $this->grid->getResource());

        $this->assertFalse($this->grid->hasColumns());
        $this->assertEmpty($this->grid->getColumns());
        $this->assertFalse($this->grid->hasColumn('foo'));

        $this->assertFalse($this->grid->hasFilters());
        $this->assertEmpty($this->grid->getFilters());
        $this->assertFalse($this->grid->hasFilter('foo'));

        $this->assertFalse($this->grid->hasSorts());
        $this->assertEmpty($this->grid->getSorts());
        $this->assertFalse($this->grid->hasSort('foo'));

        $this->assertFalse($this->grid->hasGlobalActions());
        $this->assertEmpty($this->grid->getGlobalActions());
        $this->assertFalse($this->grid->hasGlobalAction('foo'));

        $this->assertFalse($this->grid->hasColumnActions());
        $this->assertEmpty($this->grid->getColumnActions());
        $this->assertFalse($this->grid->hasColumnAction('foo'));

        $this->assertFalse($this->grid->hasBatches());
        $this->assertEmpty($this->grid->getBatches());
        $this->assertFalse($this->grid->hasBatch('foo'));

        $this->assertFalse($this->grid->hasData());
        $this->assertEmpty($this->grid->getData());

        $this->assertFalse($this->grid->hasOptions());
        $this->assertEmpty($this->grid->getOptions());
        $this->assertFalse($this->grid->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->grid = new Grid(
            $this->resource,
            $columns = [$columnName = 'column' => $column = $this->createColumnMock()],
            $filters = [$filterName = 'filter' => $filter = $this->createFilterMock()],
            $sorts = [$sortName = 'sort' => $sort = $this->createSortMock()],
            $globalActions = [$globalActionName = 'global_action' => $globalAction = $this->createActionMock()],
            $columnActions = [$columnActionName = 'column_action' => $columnAction = $this->createActionMock()],
            $batches = [$batchName = 'batch' => $batch = $this->createBatchMock()],
            $data = ['data'],
            $options = [$optionName = 'foo' => $option = 'bar']
        );

        $this->assertTrue($this->grid->hasColumns());
        $this->assertSame($columns, $this->grid->getColumns());
        $this->assertTrue($this->grid->hasColumn($columnName));
        $this->assertSame($column, $this->grid->getColumn($columnName));

        $this->assertTrue($this->grid->hasFilters());
        $this->assertSame($filters, $this->grid->getFilters());
        $this->assertTrue($this->grid->hasFilter($filterName));
        $this->assertSame($filter, $this->grid->getFilter($filterName));

        $this->assertTrue($this->grid->hasSorts());
        $this->assertSame($sorts, $this->grid->getSorts());
        $this->assertTrue($this->grid->hasSort($sortName));
        $this->assertSame($sort, $this->grid->getSort($sortName));

        $this->assertTrue($this->grid->hasGlobalActions());
        $this->assertSame($globalActions, $this->grid->getGlobalActions());
        $this->assertTrue($this->grid->hasGlobalAction($globalActionName));
        $this->assertSame($globalAction, $this->grid->getGlobalAction($globalActionName));

        $this->assertTrue($this->grid->hasColumnActions());
        $this->assertSame($columnActions, $this->grid->getColumnActions());
        $this->assertTrue($this->grid->hasColumnAction($columnActionName));
        $this->assertSame($columnAction, $this->grid->getColumnAction($columnActionName));

        $this->assertTrue($this->grid->hasBatches());
        $this->assertSame($batches, $this->grid->getBatches());
        $this->assertTrue($this->grid->hasBatch($batchName));
        $this->assertSame($batch, $this->grid->getBatch($batchName));

        $this->assertTrue($this->grid->hasData());
        $this->assertSame($data, $this->grid->getData());

        $this->assertTrue($this->grid->hasOptions());
        $this->assertSame($options, $this->grid->getOptions());
        $this->assertTrue($this->grid->hasOption($optionName));
        $this->assertSame($option, $this->grid->getOption($optionName));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidArgumentException
     * @expectedExceptionMessage The column "foo" could not be found.
     */
    public function testMissingColumn()
    {
        $this->grid->getColumn('foo');
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidArgumentException
     * @expectedExceptionMessage The filter "foo" could not be found.
     */
    public function testMissingFilter()
    {
        $this->grid->getFilter('foo');
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidArgumentException
     * @expectedExceptionMessage The sort "foo" could not be found.
     */
    public function testMissingSort()
    {
        $this->grid->getSort('foo');
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidArgumentException
     * @expectedExceptionMessage The global action "foo" could not be found.
     */
    public function testMissingGlobalAction()
    {
        $this->grid->getGlobalAction('foo');
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidArgumentException
     * @expectedExceptionMessage The column action "foo" could not be found.
     */
    public function testMissingColumnAction()
    {
        $this->grid->getColumnAction('foo');
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidArgumentException
     * @expectedExceptionMessage The batch "foo" could not be found.
     */
    public function testMissingBatch()
    {
        $this->grid->getBatch('foo');
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The grid option "foo" could not be found.
     */
    public function testMissingOption()
    {
        $this->grid->getOption('foo');
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
