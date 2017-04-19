<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Rest\View;

use FOS\RestBundle\View\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\PagerfantaViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PagerfantaViewSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PagerfantaViewSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormRendererInterface
     */
    private $pagerfantaFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->pagerfantaFactory = $this->createPagerfantaFactoryMock();
        $this->requestStack = $this->createRequestStackMock();

        $this->subscriber = new PagerfantaViewSubscriber(
            $this->parameterResolver,
            $this->pagerfantaFactory,
            $this->requestStack
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [RestEvents::VIEW => [
                ['onApi', -3000],
                ['onView', -3000],
            ]],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testApiWithoutApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->never())
            ->method('getView');

        $this->subscriber->onApi($event);
    }

    public function testApiWithoutPagerfanta()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('data'));

        $view
            ->expects($this->never())
            ->method('setData');

        $this->subscriber->onApi($event);
    }

    public function testApiWithPagerfanta()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($pagerfanta = $this->createPagerfantaMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveCurrentPage')
            ->will($this->returnValue($currentPage = 2));

        $pagerfanta
            ->expects($this->once())
            ->method('setCurrentPage')
            ->with($this->identicalTo($currentPage));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveMaxPerPage')
            ->will($this->returnValue($maxPerPage = 20));

        $pagerfanta
            ->expects($this->once())
            ->method('setMaxPerPage')
            ->with($this->identicalTo($maxPerPage));

        $pagerfanta
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator($values = ['value'])));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($values));

        $this->subscriber->onApi($event);
    }

    public function testApiWithHateoas()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveHateoas')
            ->will($this->returnValue(true));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($pagerfanta = $this->createPagerfantaMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveCurrentPage')
            ->will($this->returnValue($currentPage = 2));

        $pagerfanta
            ->expects($this->once())
            ->method('setCurrentPage')
            ->with($this->identicalTo($currentPage));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveMaxPerPage')
            ->will($this->returnValue($maxPerPage = 20));

        $pagerfanta
            ->expects($this->once())
            ->method('setMaxPerPage')
            ->with($this->identicalTo($maxPerPage));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['_route', null, $route = 'route'],
                ['_route_params', [], $routeParameters = ['foo' => 'bar']],
            ]));

        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($queryParameters = ['baz' => 'bat']));

        $this->pagerfantaFactory
            ->expects($this->once())
            ->method('createRepresentation')
            ->with(
                $this->identicalTo($pagerfanta),
                $this->callback(function ($config) use ($route, $routeParameters, $queryParameters) {
                    return $config instanceof Route
                        && $config->getName() === $route
                        && $config->getParameters() === array_merge($routeParameters, $queryParameters);
                })
            )
            ->will($this->returnValue($representation = 'representation'));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($representation));

        $this->subscriber->onApi($event);
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException
     * @expectedExceptionMessage The request could not be found.
     */
    public function testApiWithHateoasButWithoutRequest()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveHateoas')
            ->will($this->returnValue(true));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($pagerfanta = $this->createPagerfantaMock()));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue(null));

        $this->subscriber->onApi($event);
    }

    public function testViewWithApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->never())
            ->method('getView');

        $this->subscriber->onView($event);
    }

    public function testViewWithoutPagerfanta()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('data'));

        $view
            ->expects($this->never())
            ->method('setTemplateVar');

        $this->subscriber->onView($event);
    }

    public function testViewWithPagerfanta()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($this->createPagerfantaMock()));

        $event
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $view
            ->expects($this->once())
            ->method('setTemplateVar')
            ->with($this->identicalTo($name.'s'));

        $this->subscriber->onView($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PagerfantaFactory
     */
    private function createPagerfantaFactoryMock()
    {
        return $this->createMock(PagerfantaFactory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->createMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ViewEvent
     */
    private function createViewEventMock()
    {
        return $this->createMock(ViewEvent::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|View
     */
    private function createViewMock()
    {
        return $this->createMock(View::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Pagerfanta
     */
    private function createPagerfantaMock()
    {
        return $this->createMock(Pagerfanta::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createRequestMock()
    {
        $request = $this->createMock(Request::class);

        $request->attributes = $this->createParameterBagMock();
        $request->query = $this->createParameterBagMock();

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterBag
     */
    private function createParameterBagMock()
    {
        return $this->createMock(ParameterBag::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
