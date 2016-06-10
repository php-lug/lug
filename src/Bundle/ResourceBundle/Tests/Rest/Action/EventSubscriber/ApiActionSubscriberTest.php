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
use Lug\Bundle\ResourceBundle\Rest\Action\EventSubscriber\ApiActionSubscriber;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ApiActionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiActionSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->urlGenerator = $this->createUrlGeneratorMock();

        $this->subscriber = new ApiActionSubscriber($this->parameterResolver, $this->urlGenerator);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([RestEvents::ACTION => ['onAction', -1000]], $this->subscriber->getSubscribedEvents());
    }

    public function testActionWithoutApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createActionEventMock();
        $event
            ->expects($this->never())
            ->method('setView');

        $this->subscriber->onAction($event);
    }

    public function testActionWithoutForm()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createActionEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue(null));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) {
                return $view->getStatusCode() === null && $view->getData() === null;
            }));

        $this->subscriber->onAction($event);
    }

    public function testActionWithInvalidForm()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createActionEventMock();
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
                return $view->getStatusCode() === Response::HTTP_BAD_REQUEST && $view->getData() === $form;
            }));

        $this->subscriber->onAction($event);
    }

    public function testActionWithValidFormAndNoContentStatusCode()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createActionEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $event
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode = Response::HTTP_NO_CONTENT));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) use ($statusCode) {
                return $view->getStatusCode() === $statusCode && $view->getData() === null;
            }));

        $this->subscriber->onAction($event);
    }

    public function testActionWithValidFormAndCreatedStatusCode()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createActionEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $form
            ->expects($this->exactly(2))
            ->method('getData')
            ->will($this->returnValue($data = new \stdClass()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveLocationRoute')
            ->will($this->returnValue($route = 'route'));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveLocationRouteParameters')
            ->with($this->identicalTo($data))
            ->will($this->returnValue($routeParameters = ['route_param']));

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($this->identicalTo($route), $this->identicalTo($routeParameters))
            ->will($this->returnValue($url = 'url'));

        $event
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode = Response::HTTP_CREATED));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) use ($statusCode, $data, $url) {
                $headers = $view->getHeaders();

                return $view->getStatusCode() === $statusCode
                    && $view->getData() === $data
                    && isset($headers['location'])
                    && $headers['location'] === [$url];
            }));

        $this->subscriber->onAction($event);
    }

    public function testActionWithValidFormAndOkStatusCode()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createActionEventMock();
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

        $event
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode = Response::HTTP_OK));

        $event
            ->expects($this->once())
            ->method('setView')
            ->with($this->callback(function (View $view) use ($statusCode, $data) {
                return $view->getStatusCode() === $statusCode && $view->getData() === $data;
            }));

        $this->subscriber->onAction($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UrlGeneratorInterface
     */
    private function createUrlGeneratorMock()
    {
        return $this->createMock(UrlGeneratorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionEvent
     */
    private function createActionEventMock()
    {
        return $this->createMock(ActionEvent::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->createMock(FormInterface::class);
    }
}
