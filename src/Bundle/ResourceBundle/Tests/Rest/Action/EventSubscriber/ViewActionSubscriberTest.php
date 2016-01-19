<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Rest\EventSubscriber;

use FOS\RestBundle\View\View;
use Lug\Bundle\ResourceBundle\Rest\Action\ActionEvent;
use Lug\Bundle\ResourceBundle\Rest\Action\EventSubscriber\ViewActionSubscriber;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ViewActionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewActionSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->subscriber = new ViewActionSubscriber($this->parameterResolver);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([RestEvents::ACTION => ['onAction', -1000]], $this->subscriber->getSubscribedEvents());
    }

    public function testActionWithApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $event = $this->createActionEventMock();
        $event
            ->expects($this->never())
            ->method('setView');

        $this->subscriber->onAction($event);
    }

    public function testActionWithInvalidForm()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $event = $this->createActionEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) use ($form) {
                return $view->getData() === $form && $view->getStatusCode() === null;
            }));

        $this->subscriber->onAction($event);
    }

    public function testActionWithValidForm()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $event = $this->createActionEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $form
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data = new \stdClass()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRoute')
            ->will($this->returnValue($route = 'route'));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRouteParametersForward')
            ->will($this->returnValue($forward = false));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRouteParameters')
            ->with($this->identicalTo($data), $this->identicalTo($forward))
            ->will($this->returnValue($routeParameters = ['route_param']));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) use ($route, $routeParameters) {
                return $view->getStatusCode() === Response::HTTP_FOUND
                    && $view->getRoute() === $route
                    && $view->getRouteParameters() === $routeParameters;
            }));

        $this->subscriber->onAction($event);
    }

    public function testActionWithoutForm()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $event = $this->createActionEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue(null));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRoute')
            ->will($this->returnValue($route = 'route'));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) use ($route) {
                return $view->getRoute() === $route && $view->getRouteParameters() === [];
            }));

        $this->subscriber->onAction($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionEvent
     */
    private function createActionEventMock()
    {
        return $this->getMockBuilder(ActionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }
}
