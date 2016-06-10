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
use Lug\Bundle\GridBundle\Form\Type\Batch\GridBatchType;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Bundle\ResourceBundle\Form\FormFactoryInterface;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\GridViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridViewSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridViewSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormFactoryInterface
     */
    private $formFactory;

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
        $this->formFactory = $this->createFormFactoryMock();
        $this->formRenderer = $this->createFormRendererMock();

        $this->subscriber = new GridViewSubscriber($this->parameterResolver, $this->formFactory, $this->formRenderer);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [RestEvents::VIEW => [
                ['onApi', -2000],
                ['onView', -1000],
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

    public function testApiWithoutGrid()
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

    public function testApiWithGrid()
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
            ->will($this->returnValue(['grid' => $gridView = $this->createGridViewMock()]));

        $gridView
            ->expects($this->once())
            ->method('getDataSource')
            ->will($this->returnValue($dataSource = 'data_source'));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($dataSource));

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

    public function testViewWithoutGrid()
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

    public function testViewWithoutBatchForm()
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
            ->will($this->returnValue(['grid' => $gridView = $this->createGridViewMock()]));

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo(GridBatchType::class),
                $this->isNull(),
                $this->identicalTo(['grid' => $gridView])
            )
            ->will($this->returnValue($batchForm = $this->createFormMock()));

        $batchForm
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($batchFormView = $this->createFormViewMock()));

        $gridView
            ->expects($this->once())
            ->method('setBatchForm')
            ->with($this->identicalTo($batchFormView));

        $view
            ->expects($this->once())
            ->method('setTemplateVar')
            ->with($this->identicalTo('grid'))
            ->will($this->returnSelf());

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($gridView));

        $this->subscriber->onView($event);
    }

    public function testViewWithBatchForm()
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
            ->will($this->returnValue([
                'grid'       => $gridView = $this->createGridViewMock(),
                'batch_form' => $batchForm = $this->createFormMock(),
            ]));

        $batchForm
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($batchFormView = $this->createFormViewMock()));

        $gridView
            ->expects($this->once())
            ->method('setBatchForm')
            ->with($this->identicalTo($batchFormView));

        $view
            ->expects($this->once())
            ->method('setTemplateVar')
            ->with($this->identicalTo('grid'))
            ->will($this->returnSelf());

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($gridView));

        $this->subscriber->onView($event);
    }

    public function testViewFormThemes()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveThemes')
            ->will($this->returnValue($themes = ['theme']));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $view
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([
                'grid'       => $gridView = $this->createGridViewMock(),
                'batch_form' => $batchForm = $this->createFormMock(),
            ]));

        $batchForm
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($batchFormView = $this->createFormViewMock()));

        $gridView
            ->expects($this->once())
            ->method('setBatchForm')
            ->with($this->identicalTo($batchFormView));

        $gridView
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($formView = $this->createFormViewMock()));

        $gridView
            ->expects($this->exactly(2))
            ->method('getBatchForm')
            ->willReturnOnConsecutiveCalls(null, $batchFormView);

        $this->formRenderer
            ->expects($this->exactly(2))
            ->method('setTheme')
            ->withConsecutive(
                [$formView, $themes],
                [$batchFormView, $themes]
            );

        $view
            ->expects($this->once())
            ->method('setTemplateVar')
            ->with($this->identicalTo('grid'))
            ->will($this->returnSelf());

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($gridView));

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
     * @return \PHPUnit_Framework_MockObject_MockObject|FormFactoryInterface
     */
    private function createFormFactoryMock()
    {
        return $this->createMock(FormFactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormRendererInterface
     */
    private function createFormRendererMock()
    {
        return $this->createMock(FormRendererInterface::class);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->createMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormView
     */
    private function createFormViewMock()
    {
        return $this->createMock(FormView::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }
}
