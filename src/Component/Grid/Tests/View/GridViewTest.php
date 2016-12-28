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
        $this->grid
            ->expects($this->exactly(2))
            ->method('getOptions')
            ->will($this->returnValue($options = ['foo' => 'bar']));

        $this->dataSourceBuilder
            ->expects($this->once())
            ->method('createDataSource')
            ->with($this->identicalTo($options))
            ->will($this->returnValue($dataSource = $this->createDataSourceMock()));

        $this->assertSame($dataSource, $this->view->getDataSource());
        $this->assertSame($dataSource, $this->view->getDataSource());
    }

    public function testDataSourceWithOptions()
    {
        $gridOptions = ['foo' => 'bar'];
        $userOptions = ['baz' => 'bat'];

        $sortedOptions = array_merge($gridOptions, $userOptions);
        ksort($sortedOptions);

        $this->grid
            ->expects($this->exactly(2))
            ->method('getOptions')
            ->will($this->returnValue($gridOptions));

        $this->dataSourceBuilder
            ->expects($this->once())
            ->method('createDataSource')
            ->with($this->identicalTo($sortedOptions))
            ->will($this->returnValue($dataSource = $this->createDataSourceMock()));

        $this->assertSame($dataSource, $this->view->getDataSource($userOptions));
        $this->assertSame($dataSource, $this->view->getDataSource($sortedOptions));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceInterface
     */
    private function createDataSourceMock()
    {
        return $this->createMock(DataSourceInterface::class);
    }
}
