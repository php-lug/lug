<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Lug\Bundle\ResourceBundle\Form\EventSubscriber\CollectionSubscriber;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CollectionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectionSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $managerRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resourceRegistry = $this->createServiceRegistryMock();
        $this->managerRegistry = $this->createServiceRegistryMock();

        $this->subscriber = new CollectionSubscriber($this->resourceRegistry, $this->managerRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [
                FormEvents::POST_SET_DATA => 'init',
                FormEvents::POST_SUBMIT   => 'manage',
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testEventWithoutInitCollection()
    {
        $initEvent = $this->createFormEventMock();
        $initEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('data'));

        $this->managerRegistry
            ->expects($this->never())
            ->method('offsetGet');

        $this->subscriber->init($initEvent);
        $this->subscriber->manage($this->createFormEventMock());
    }

    public function testEventWithCollections()
    {
        $initEvent = $this->createFormEventMock();
        $initEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($initCollection = $this->createCollectionMock()));

        $initCollection
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$element = new \stdClass()])));

        $manageEvent = $this->createFormEventMock();
        $manageEvent
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $manageEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($manageCollection = $this->createCollectionMock()));

        $manageCollection
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($element))
            ->will($this->returnValue(true));

        $this->managerRegistry
            ->expects($this->never())
            ->method('offsetGet');

        $this->subscriber->init($initEvent);
        $this->subscriber->manage($manageEvent);
    }

    public function testEventWithoutManageCollection()
    {
        $initEvent = $this->createFormEventMock();
        $initEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($initCollection = $this->createCollectionMock()));

        $manageEvent = $this->createFormEventMock();
        $manageEvent
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $manageEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('data'));

        $this->managerRegistry
            ->expects($this->never())
            ->method('offsetGet');

        $this->subscriber->init($initEvent);
        $this->subscriber->manage($manageEvent);
    }

    public function testEventWithRemovedItem()
    {
        $initEvent = $this->createFormEventMock();
        $initEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($initCollection = $this->createCollectionMock()));

        $initCollection
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([
                $element1 = new \stdClass(),
                $element2 = new \stdClass(),
            ])));

        $manageEvent = $this->createFormEventMock();
        $manageEvent
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $manageEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($manageCollection = $this->createCollectionMock()));

        $manageCollection
            ->expects($this->exactly(2))
            ->method('contains')
            ->withConsecutive([$element1], [$element2])
            ->will($this->returnValue(false));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($class = get_class($element1)));

        $resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->managerRegistry
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($manager = $this->createObjectManagerMock()));

        $manager
            ->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive([$element1], [$element2]);

        $this->subscriber->init($initEvent);
        $this->subscriber->manage($manageEvent);
    }

    public function testEventWithInvalidForm()
    {
        $initEvent = $this->createFormEventMock();
        $initEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($initCollection = $this->createCollectionMock()));

        $manageEvent = $this->createFormEventMock();
        $manageEvent
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->managerRegistry
            ->expects($this->never())
            ->method('getManagerForClass');

        $this->subscriber->init($initEvent);
        $this->subscriber->manage($manageEvent);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormEvent
     */
    private function createFormEventMock()
    {
        return $this->getMockBuilder(FormEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Collection
     */
    private function createCollectionMock()
    {
        return $this->getMock(Collection::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    private function createObjectManagerMock()
    {
        return $this->getMock(ObjectManager::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
