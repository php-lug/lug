<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\EventListener;

use Lug\Bundle\ResourceBundle\EventListener\MessageListener;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Domain\DomainEvent;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageListener
     */
    private $messageListener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->translator = $this->createTranslatorMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->messageListener = new MessageListener(
            $this->translator,
            $this->propertyAccessor,
            $this->parameterResolver
        );
    }

    public function testMessageWithExplicit()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->once())
            ->method('getMessageType')
            ->will($this->returnValue($messageType = 'message_type'));

        $event
            ->expects($this->once())
            ->method('setMessageType')
            ->with($this->identicalTo($messageType));

        $event
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($message = 'message'));

        $event
            ->expects($this->once())
            ->method('setMessage')
            ->with($this->identicalTo($message));

        $this->messageListener->addMessage($event);
    }

    public function testMessageTypeSuccess()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->once())
            ->method('getMessageType')
            ->will($this->returnValue(null));

        $event
            ->expects($this->once())
            ->method('isStopped')
            ->will($this->returnValue(false));

        $event
            ->expects($this->once())
            ->method('setMessageType')
            ->with($this->identicalTo('success'));

        $event
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($message = 'message'));

        $event
            ->expects($this->once())
            ->method('setMessage')
            ->with($this->identicalTo($message));

        $this->messageListener->addMessage($event);
    }

    public function testMessageTypeError()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->once())
            ->method('getMessageType')
            ->will($this->returnValue(null));

        $event
            ->expects($this->once())
            ->method('isStopped')
            ->will($this->returnValue(true));

        $event
            ->expects($this->once())
            ->method('setMessageType')
            ->with($this->identicalTo('error'));

        $event
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($message = 'message'));

        $event
            ->expects($this->once())
            ->method('setMessage')
            ->with($this->identicalTo($message));

        $this->messageListener->addMessage($event);
    }

    public function testMessageWithLabelPropertyPath()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->once())
            ->method('getMessageType')
            ->will($this->returnValue($messageType = 'message_type'));

        $event
            ->expects($this->once())
            ->method('setMessageType')
            ->with($this->identicalTo($messageType));

        $event
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue(null));

        $event
            ->expects($this->once())
            ->method('getAction')
            ->will($this->returnValue($action = 'action'));

        $event
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data = new \stdClass()));

        $event
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($labelPropertyPath = 'property_path'));

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data),
                $this->identicalTo($labelPropertyPath)
            )
            ->will($this->returnValue($property = 'property'));

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with(
                $this->identicalTo('lug.'.$name.'.'.$action.'.'.$messageType),
                $this->identicalTo(['%'.$name.'%' => $property])
            )
            ->will($this->returnValue($translation = 'translation'));

        $event
            ->expects($this->once())
            ->method('setMessage')
            ->with($this->identicalTo($translation));

        $this->messageListener->addMessage($event);
    }

    public function testMessageWithoutLabelPropertyPath()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->once())
            ->method('getMessageType')
            ->will($this->returnValue($messageType = 'message_type'));

        $event
            ->expects($this->once())
            ->method('setMessageType')
            ->with($this->identicalTo($messageType));

        $event
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue(null));

        $event
            ->expects($this->once())
            ->method('getAction')
            ->will($this->returnValue($action = 'action'));

        $event
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data = $this->createObjectMock()));

        $data
            ->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue($property = 'property'));

        $event
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with(
                $this->identicalTo('lug.'.$name.'.'.$action.'.'.$messageType),
                $this->identicalTo(['%'.$name.'%' => $property])
            )
            ->will($this->returnValue($translation = 'translation'));

        $event
            ->expects($this->once())
            ->method('setMessage')
            ->with($this->identicalTo($translation));

        $this->messageListener->addMessage($event);
    }

    public function testMessageWithApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->never())
            ->method('setMessage');

        $this->messageListener->addMessage($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private function createTranslatorMock()
    {
        return $this->createMock(TranslatorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->createMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainEvent
     */
    private function createDomainEventMock()
    {
        return $this->createMock(DomainEvent::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\stdClass
     */
    private function createObjectMock()
    {
        return $this->getMockBuilder(\stdClass::class)
            ->setMethods(['__toString'])
            ->getMock();
    }
}
