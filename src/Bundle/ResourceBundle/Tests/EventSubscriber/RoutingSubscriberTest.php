<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\EventSubscriber;

use Lug\Bundle\ResourceBundle\EventSubscriber\RoutingSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RoutingSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoutingSubscriber
     */
    private $routingSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->routingSubscriber = new RoutingSubscriber();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->routingSubscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 31]],
            $this->routingSubscriber->getSubscribedEvents()
        );
    }

    public function testKernelRequestWithParameters()
    {
        $event = $this->createGetResponseEventMock();
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_route_params'), $this->identicalTo([]))
            ->will($this->returnValue(array_merge(['_lug_param' => 'foo'], $routeParams = ['bar' => 'bar'])));

        $request->attributes
            ->expects($this->once())
            ->method('set')
            ->with($this->identicalTo('_route_params'), $this->identicalTo($routeParams));

        $this->routingSubscriber->onKernelRequest($event);
    }

    public function testKernelRequestWithoutParameters()
    {
        $event = $this->createGetResponseEventMock();
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_route_params'), $this->identicalTo([]))
            ->will($this->returnValue([]));

        $request->attributes
            ->expects($this->never())
            ->method('set');

        $this->routingSubscriber->onKernelRequest($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GetResponseEvent
     */
    private function createGetResponseEventMock()
    {
        return $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createRequestMock()
    {
        $request = $this->getMock(Request::class);
        $request->attributes = $this->createParameterBagMock();

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterBag
     */
    private function createParameterBagMock()
    {
        return $this->getMock(ParameterBag::class);
    }
}
