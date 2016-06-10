<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Renderer;

use Lug\Bundle\GridBundle\Renderer\Renderer;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Component\Grid\Action\ActionRendererInterface;
use Lug\Component\Grid\Column\ColumnRendererInterface;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Renderer\RendererInterface;
use Lug\Component\Grid\Sort\SorterInterface;
use Lug\Component\Grid\Sort\SorterRendererInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    private $twig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ActionRendererInterface
     */
    private $actionRenderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ColumnRendererInterface
     */
    private $columnRenderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SorterRendererInterface
     */
    private $sorterRenderer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->twig = $this->createTwigEnvironmentMock();
        $this->actionRenderer = $this->createActionRendererMock();
        $this->columnRenderer = $this->createColumnRendererMock();
        $this->sorterRenderer = $this->createSorterRendererMock();

        $this->renderer = new Renderer(
            $this->twig,
            $this->actionRenderer,
            $this->columnRenderer,
            $this->sorterRenderer
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RendererInterface::class, $this->renderer);
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRender($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'grid';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo(['grid' => $view])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->render($view));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderFilters($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'filters';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo(['grid' => $view])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderFilters($view));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderGrid($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'body';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo(['grid' => $view])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderGrid($view));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderColumn($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'column';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->columnRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($view),
                $this->identicalTo($column = $this->createColumnMock()),
                $this->identicalTo($data = new \stdClass())
            )
            ->will($this->returnValue($value = 'value'));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo([
                    'column' => $column,
                    'data'   => $data,
                    'value'  => $value,
                    'grid'   => $view,
                ])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderColumn($view, $column, $data));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderColumnSortings($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'column_sortings';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo([
                    'column' => $column = $this->createColumnMock(),
                    'grid'   => $view,
                ])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderColumnSortings($view, $column));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderColumnSorting($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'column_sorting';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->sorterRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($view),
                $this->identicalTo($column = $this->createColumnMock()),
                $this->identicalTo($sorting = SorterInterface::ASC)
            )
            ->will($this->returnValue($value = 'value'));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo([
                    'column'  => $column,
                    'sorting' => $sorting,
                    'label'   => 'lug.sorting.'.strtolower($sorting),
                    'value'   => $value,
                    'grid'    => $view,
                ])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderColumnSorting($view, $column, $sorting));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderColumnActions($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'column_actions';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo([
                    'data' => $data = new \stdClass(),
                    'grid' => $view,
                ])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderColumnActions($view, $data));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderColumnAction($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'column_action';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->actionRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($view),
                $this->identicalTo($action = $this->createActionMock()),
                $this->identicalTo($data = new \stdClass())
            )
            ->will($this->returnValue($value = 'value'));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo([
                    'action' => $action,
                    'data'   => $data,
                    'value'  => $value,
                    'grid'   => $view,
                ])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderColumnAction($view, $action, $data));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderGlobalActions($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'global_actions';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo(['grid' => $view])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderGlobalActions($view));
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRenderGlobalAction($gridTemplate = null, $rendererTemplate = null)
    {
        $template = 'global_action';

        if ($rendererTemplate !== null) {
            $this->renderer = new Renderer(
                $this->twig,
                $this->actionRenderer,
                $this->columnRenderer,
                $this->sorterRenderer,
                [$template => $rendererTemplate]
            );
        }

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo($option = $template.'_template'))
            ->will($this->returnValue($gridTemplate !== null));

        $grid
            ->expects($gridTemplate !== null ? $this->once() : $this->never())
            ->method('getOption')
            ->with($this->identicalTo($option))
            ->will($this->returnValue($gridTemplate));

        $this->actionRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($view),
                $this->identicalTo($action = $this->createActionMock()),
                $this->isNull()
            )
            ->will($this->returnValue($value = 'value'));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($gridTemplate ?: ($rendererTemplate ?: '@LugGrid/'.$template.'.html.twig')),
                $this->identicalTo([
                    'action' => $action,
                    'value'  => $value,
                    'grid'   => $view,
                ])
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->renderGlobalAction($view, $action));
    }

    /**
     * @return mixed[]
     */
    public function renderProvider()
    {
        return [
            'default_template'  => [],
            'grid_template'     => ['grid_template'],
            'renderer_template' => [null, 'grid_template'],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    public function createTwigEnvironmentMock()
    {
        return $this->createMock(\Twig_Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionRendererInterface
     */
    private function createActionRendererMock()
    {
        return $this->createMock(ActionRendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnRendererInterface
     */
    private function createColumnRendererMock()
    {
        return $this->createMock(ColumnRendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SorterRendererInterface
     */
    private function createSorterRendererMock()
    {
        return $this->createMock(SorterRendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->createMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }
}
