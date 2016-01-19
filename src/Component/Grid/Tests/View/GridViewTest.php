<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\View;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\DataSource\DataSourceInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\View\GridView;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridView
     */
    private $view;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private $grid;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private $dataSourceBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->grid = $this->createGridMock();
        $this->dataSourceBuilder = $this->createDataSourceBuilderMock();

        $this->view = new GridView($this->grid, $this->dataSourceBuilder);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridViewInterface::class, $this->view);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->grid, $this->view->getDefinition());
    }

    public function testDataSource()
    {
        $this->dataSourceBuilder
            ->expects($this->once())
            ->method('createDataSource')
            ->with($this->identicalTo([]))
            ->will($this->returnValue($dataSource = $this->createDataSourceMock()));

        $this->assertSame($dataSource, $this->view->getDataSource());
        $this->assertSame($dataSource, $this->view->getDataSource());
    }

    public function testDataSourceWithOptions()
    {
        $options = $sortedOptions = ['foo' => 'bar', 'baz' => 'bat'];
        ksort($sortedOptions);

        $this->dataSourceBuilder
            ->expects($this->once())
            ->method('createDataSource')
            ->with($this->identicalTo($sortedOptions))
            ->will($this->returnValue($dataSource = $this->createDataSourceMock()));

        $this->assertSame($dataSource, $this->view->getDataSource($options));
        $this->assertSame($dataSource, $this->view->getDataSource($sortedOptions));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->getMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceInterface
     */
    private function createDataSourceMock()
    {
        return $this->getMock(DataSourceInterface::class);
    }
}
