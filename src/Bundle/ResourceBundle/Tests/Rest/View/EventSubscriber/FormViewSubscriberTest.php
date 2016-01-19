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
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\FormViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormViewSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormViewSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormRendererInterface
     */
    private $formRenderer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->formRenderer = $this->createFormRendererMock();

        $this->subscriber = new FormViewSubscriber($this->parameterResolver, $this->formRenderer);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [RestEvents::VIEW => [
                ['onApi', -1000],
                ['onView', -2000],
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

    public function testApiWithoutForms()
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

    public function testApiWithForms()
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
            ->will($this->returnValue([
                new \stdClass(),
                $formNotSubmitted = $this->createFormMock(),
                $formValid = $this->createFormMock(),
                $formInvalid = $this->createFormMock(),
            ]));

        $formNotSubmitted
            ->expects($this->once())
            ->method('isSubmitted')
            ->will($this->returnValue(false));

        $formValid
            ->expects($this->once())
            ->method('isSubmitted')
            ->will($this->returnValue(true));

        $formValid
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $formInvalid
            ->expects($this->once())
            ->method('isSubmitted')
            ->will($this->returnValue(true));

        $formInvalid
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($formInvalid))
            ->will($this->returnSelf());

        $view
            ->expects($this->once())
            ->method('setStatusCode')
            ->with($this->identicalTo(Response::HTTP_BAD_REQUEST));

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

    public function testViewWithoutForm()
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
            ->method('setData');

        $this->subscriber->onView($event);
    }

    public function testViewWithForm()
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
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($formView = $this->createFormViewMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveThemes')
            ->will($this->returnValue($themes = ['theme']));

        $this->formRenderer
            ->expects($this->once())
            ->method('setTheme')
            ->with($this->identicalTo($formView), $this->identicalTo($themes));

        $view
            ->expects($this->once())
            ->method('setTemplateVar')
            ->with($this->identicalTo('form'))
            ->will($this->returnSelf());

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($formView));

        $this->subscriber->onView($event);
    }

    public function testViewWithForms()
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
            ->will($this->returnValue([$object = new \stdClass(), $form = $this->createFormMock()]));

        $form
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($formView = $this->createFormViewMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveThemes')
            ->will($this->returnValue($themes = ['theme']));

        $this->formRenderer
            ->expects($this->once())
            ->method('setTheme')
            ->with($this->identicalTo($formView), $this->identicalTo($themes));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo([$object, $formView]));

        $this->subscriber->onView($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormRendererInterface
     */
    private function createFormRendererMock()
    {
        return $this->getMock(FormRendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ViewEvent
     */
    private function createViewEventMock()
    {
        return $this->getMockBuilder(ViewEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|View
     */
    private function createViewMock()
    {
        return $this->getMock(View::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormView
     */
    private function createFormViewMock()
    {
        return $this->getMock(FormView::class);
    }
}
