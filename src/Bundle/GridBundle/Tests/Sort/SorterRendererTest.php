<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Sort;

use Lug\Bundle\GridBundle\Filter\FilterManagerInterface;
use Lug\Bundle\GridBundle\Sort\SorterRenderer;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Sort\SorterInterface;
use Lug\Component\Grid\Sort\SorterRendererInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SorterRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SorterRenderer
     */
    private $renderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->requestStack = $this->createRequestStackMock();
        $this->filterManager = $this->createFilterManagerMock();
        $this->urlGenerator = $this->createUrlGeneratorMock();

        $this->renderer = new SorterRenderer($this->requestStack, $this->filterManager, $this->urlGenerator);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(SorterRendererInterface::class, $this->renderer);
    }

    public function testRenderAsc()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('_route_params'),
                $this->identicalTo([])
            )
            ->will($this->returnValue($routeParams = ['route' => 'param']));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($queryParams = ['query' => 'query']));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(false));

        $grid
            ->expects($this->once())
            ->method('getOption')
            ->with($this->identicalTo('grid_route'))
            ->will($this->returnValue($route = 'route'));

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route),
                $this->identicalTo(array_merge(
                    $routeParams,
                    $queryParams,
                    ['grid' => ['sorting' => $name]]
                ))
            )
            ->will($this->returnValue($url = 'url'));

        $this->assertSame($url, $this->renderer->render($view, $column, SorterInterface::ASC));
    }

    public function testRenderDesc()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('_route_params'),
                $this->identicalTo([])
            )
            ->will($this->returnValue($routeParams = ['route' => 'param']));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($queryParams = ['query' => 'query']));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(false));

        $grid
            ->expects($this->once())
            ->method('getOption')
            ->with($this->identicalTo('grid_route'))
            ->will($this->returnValue($route = 'route'));

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route),
                $this->identicalTo(array_merge(
                    $routeParams,
                    $queryParams,
                    ['grid' => ['sorting' => '-'.$name]]
                ))
            )
            ->will($this->returnValue($url = 'url'));

        $this->assertSame($url, $this->renderer->render($view, $column, SorterInterface::DESC));
    }

    public function testRenderWithoutSort()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(false));

        $this->assertNull($this->renderer->render($view, $column, SorterInterface::ASC));
    }

    public function testRenderWithoutRequest()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue(null));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(false));

        $grid
            ->expects($this->once())
            ->method('getOption')
            ->with($this->identicalTo('grid_route'))
            ->will($this->returnValue($route = 'route'));

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route),
                $this->identicalTo(['grid' => ['sorting' => $name]])
            )
            ->will($this->returnValue($url = 'url'));

        $this->assertSame($url, $this->renderer->render($view, $column, SorterInterface::ASC));
    }

    public function testRenderWithSortedColumn()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('_route_params'),
                $this->identicalTo([])
            )
            ->will($this->returnValue([]));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue(['grid' => ['sorting' => $name]]));

        $this->assertNull($this->renderer->render($view, $column, SorterInterface::ASC));
    }

    public function testRenderWithSortedColumnReset()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('_route_params'),
                $this->identicalTo([])
            )
            ->will($this->returnValue($routeParams = ['route' => 'param']));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array_merge(
                $queryParams = ['query' => 'query'],
                ['grid' => ['reset' => true]]
            )));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(false));

        $grid
            ->expects($this->once())
            ->method('getOption')
            ->with($this->identicalTo('grid_route'))
            ->will($this->returnValue($route = 'route'));

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route),
                $this->identicalTo(array_merge(
                    $routeParams,
                    $queryParams,
                    ['grid' => ['sorting' => $name]]
                ))
            )
            ->will($this->returnValue($url = 'url'));

        $this->assertSame($url, $this->renderer->render($view, $column, SorterInterface::ASC));
    }

    public function testRenderWithPersistent()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('_route_params'),
                $this->identicalTo([])
            )
            ->will($this->returnValue($routeParams = ['route' => 'param']));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($queryParams = ['query' => 'query']));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(true));

        $grid
            ->expects($this->exactly(2))
            ->method('getOption')
            ->will($this->returnValueMap([
                ['persistent', true],
                ['grid_route', $route = 'route'],
            ]));

        $this->filterManager
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($grid))
            ->will($this->returnValue([]));

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route),
                $this->identicalTo(array_merge(
                    $routeParams,
                    $queryParams,
                    ['grid' => ['sorting' => $name]]
                ))
            )
            ->will($this->returnValue($url = 'url'));

        $this->assertSame($url, $this->renderer->render($view, $column, SorterInterface::ASC));
    }

    public function testRenderWithPersistentSorted()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view = $this->createGridViewMock();
        $view
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($grid = $this->createGridMock()));

        $grid
            ->expects($this->once())
            ->method('hasSort')
            ->with($this->identicalTo($name))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('_route_params'),
                $this->identicalTo([])
            )
            ->will($this->returnValue($routeParams = ['route' => 'param']));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($queryParams = ['query' => 'query']));

        $grid
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(true));

        $grid
            ->expects($this->once())
            ->method('getOption')
            ->with($this->identicalTo('persistent'))
            ->will($this->returnValue(true));

        $this->filterManager
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($grid))
            ->will($this->returnValue(['sorting' => $name]));

        $this->assertNull($this->renderer->render($view, $column, SorterInterface::ASC));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->createMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterManagerInterface
     */
    private function createFilterManagerMock()
    {
        return $this->createMock(FilterManagerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UrlGeneratorInterface
     */
    private function createUrlGeneratorMock()
    {
        return $this->createMock(UrlGeneratorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createRequestMock()
    {
        $request = $this->createMock(Request::class);
        $request->query = $this->createParameterBagMock();
        $request->attributes = $this->createParameterBagMock();

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterBag
     */
    private function createParameterBagMock()
    {
        return $this->createMock(ParameterBag::class);
    }
}
