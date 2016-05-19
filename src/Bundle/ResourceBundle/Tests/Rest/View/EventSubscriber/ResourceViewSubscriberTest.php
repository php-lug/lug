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

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\ResourceViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceViewSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceViewSubscriber
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

        $this->subscriber = new ResourceViewSubscriber($this->parameterResolver);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [RestEvents::VIEW => [
                ['onApi', -4000],
                ['onView', -4000],
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

    public function testApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveSerializerGroups')
            ->with($this->identicalTo($resource = $this->createResourceMock()))
            ->will($this->returnValue($serializerGroups = ['group']));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveSerializerNull')
            ->will($this->returnValue($serializerNull = true));

        $event = $this->createViewEventMock();
        $event
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view = $this->createViewMock()));

        $event
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource));

        $view
            ->expects($this->exactly(2))
            ->method('getSerializationContext')
            ->will($this->returnValue($serializationContext = $this->createSerializationContextMock()));

        $serializationContext
            ->expects($this->once())
            ->method('addGroups')
            ->with($this->identicalTo($serializerGroups));

        $serializationContext
            ->expects($this->once())
            ->method('setSerializeNull')
            ->with($this->identicalTo($serializerNull));

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

    public function testViewWithoutTemplateVar()
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
            ->will($this->returnValue($data = new \stdClass()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveTemplate')
            ->will($this->returnValue($template = 'template'));

        $view
            ->expects($this->once())
            ->method('setTemplate')
            ->with($this->identicalTo($template))
            ->will($this->returnSelf());

        $event
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $view
            ->expects($this->once())
            ->method('getTemplateVar')
            ->will($this->returnValue(null));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo(['data' => $data, 'resource' => $resource]));

        $this->subscriber->onView($event);
    }

    public function testViewWithTemplateVar()
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
            ->will($this->returnValue($data = new \stdClass()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveTemplate')
            ->will($this->returnValue($template = 'template'));

        $view
            ->expects($this->once())
            ->method('setTemplate')
            ->with($this->identicalTo($template))
            ->will($this->returnSelf());

        $event
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $view
            ->expects($this->once())
            ->method('getTemplateVar')
            ->will($this->returnValue($templateVar = 'template_var'));

        $view
            ->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo([$templateVar => $data, 'resource' => $resource]));

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
     * @return \PHPUnit_Framework_MockObject_MockObject|Context
     */
    private function createSerializationContextMock()
    {
        return $this->getMock(Context::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
