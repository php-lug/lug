<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Twig;

use Lug\Bundle\GridBundle\Twig\GridExtension;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Renderer\RendererInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Pagerfanta\Pagerfanta;
use WhiteOctober\PagerfantaBundle\Twig\PagerfantaExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridExtension
     */
    private $extension;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RendererInterface
     */
    private $renderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PagerfantaExtension
     */
    private $pagerfantaExtension;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->renderer = $this->createRendererMock();
        $this->pagerfantaExtension = $this->createPagerfantaExtensionMock();

        $this->extension = new GridExtension($this->renderer, $this->pagerfantaExtension);

        $this->twig = new \Twig_Environment(new \Twig_Loader_Array([]));
        $this->twig->addExtension($this->extension);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RendererInterface::class, $this->extension);
        $this->assertInstanceOf(\Twig_Extension::class, $this->extension);
    }

    public function testGrid()
    {
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($grid = $this->createGridViewMock()))
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig->createTemplate('{{ lug_grid(grid) }}')->render(['grid' => $grid])
        );
    }

    public function testFilters()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderFilters')
            ->with($this->identicalTo($grid = $this->createGridViewMock()))
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig->createTemplate('{{ lug_grid_filters(grid) }}')->render(['grid' => $grid])
        );
    }

    public function testBody()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderGrid')
            ->with($this->identicalTo($grid = $this->createGridViewMock()))
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig->createTemplate('{{ lug_grid_body(grid) }}')->render(['grid' => $grid])
        );
    }

    public function testColumn()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderColumn')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->identicalTo($column = $this->createColumnMock()),
                $this->identicalTo($data = 'data')
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_column(grid, column, data) }}')
                ->render(['grid' => $grid, 'column' => $column, 'data' => $data])
        );
    }

    public function testColumnActions()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderColumnActions')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->identicalTo($data = 'data')
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_column_actions(grid, data) }}')
                ->render(['grid' => $grid, 'data' => $data])
        );
    }

    public function testColumnAction()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderColumnAction')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->identicalTo($action = $this->createActionMock()),
                $this->identicalTo($data = 'data')
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_column_action(grid, action, data) }}')
                ->render(['grid' => $grid, 'action' => $action, 'data' => $data])
        );
    }

    public function testColumnSortings()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderColumnSortings')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->identicalTo($column = $this->createColumnMock())
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_column_sortings(grid, column) }}')
                ->render(['grid' => $grid, 'column' => $column])
        );
    }

    public function testColumnSorting()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderColumnSorting')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->identicalTo($column = $this->createColumnMock()),
                $this->identicalTo($sorting = 'sorting')
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_column_sorting(grid, column, sorting) }}')
                ->render(['grid' => $grid, 'column' => $column, 'sorting' => $sorting])
        );
    }

    public function testGlobalActions()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderGlobalActions')
            ->with($this->identicalTo($grid = $this->createGridViewMock()))
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig->createTemplate('{{ lug_grid_global_actions(grid) }}')->render(['grid' => $grid])
        );
    }

    public function testGlobalAction()
    {
        $this->renderer
            ->expects($this->once())
            ->method('renderGlobalAction')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->identicalTo($action = $this->createActionMock())
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_global_action(grid, action) }}')
                ->render(['grid' => $grid, 'action' => $action])
        );
    }

    public function testPager()
    {
        $this->pagerfantaExtension
            ->expects($this->once())
            ->method('renderPagerfanta')
            ->with(
                $this->identicalTo($pager = $this->createPagerfantaMock()),
                $this->identicalTo($name = 'name'),
                $this->identicalTo($options = ['foo' => 'bar'])
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_pager(pager, name, options) }}')
                ->render(['pager' => $pager, 'name' => $name, 'options' => $options])
        );
    }

    public function testPagerWithReset()
    {
        $this->pagerfantaExtension
            ->expects($this->once())
            ->method('renderPagerfanta')
            ->with(
                $this->identicalTo($pager = $this->createPagerfantaMock()),
                $this->identicalTo($name = 'name'),
                $this->identicalTo($options = ['routeParams' => ['foo' => 'bar']])
            )
            ->will($this->returnValue($result = '<div>result</div>'));

        $options['routeParams']['grid']['baz'] = 'bat';
        $options['routeParams']['grid']['reset'] = true;

        $this->assertSame(
            $result,
            $this->twig
                ->createTemplate('{{ lug_grid_pager(pager, name, options) }}')
                ->render(['pager' => $pager, 'name' => $name, 'options' => $options])
        );
    }

    public function testName()
    {
        $this->assertSame('lug_grid', $this->extension->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RendererInterface
     */
    private function createRendererMock()
    {
        return $this->getMock(RendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PagerfantaExtension
     */
    private function createPagerfantaExtensionMock()
    {
        return $this->getMockBuilder(PagerfantaExtension::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->getMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->getMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Pagerfanta
     */
    private function createPagerfantaMock()
    {
        return $this->getMockBuilder(Pagerfanta::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
