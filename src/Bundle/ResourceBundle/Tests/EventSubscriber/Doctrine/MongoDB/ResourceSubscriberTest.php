<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\EventSubscriber\Doctrine\MongoDB;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB\ResourceSubscriber;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceSubscriber
     */
    private $resourceSubscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $serviceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists(LoadClassMetadataEventArgs::class)) {
            $this->markTestSkipped();
        }

        $this->serviceRegistry = $this->createServiceRegistryMock();
        $this->resourceSubscriber = new ResourceSubscriber($this->serviceRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->resourceSubscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([Events::loadClassMetadata], $this->resourceSubscriber->getSubscribedEvents());
    }

    public function testLoadClassMetadataWithResource()
    {
        $event = $this->createLoadClassMetadataEventArgsMock();
        $event
            ->expects($this->once())
            ->method('getClassMetadata')
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $classMetadata
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($model = 'model'));

        $this->serviceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model));

        $resource
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository = 'repository'));

        $classMetadata
            ->expects($this->once())
            ->method('setCustomRepositoryClass')
            ->with($this->identicalTo($repository));

        $classMetadata->isMappedSuperclass = true;

        $this->resourceSubscriber->loadClassMetadata($event);

        $this->assertFalse($classMetadata->isMappedSuperclass);
    }

    public function testLoadClassMetadataWithoutResource()
    {
        $event = $this->createLoadClassMetadataEventArgsMock();
        $event
            ->expects($this->once())
            ->method('getClassMetadata')
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $classMetadata
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($model = 'model'));

        $this->serviceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([])));

        $classMetadata
            ->expects($this->never())
            ->method('setCustomRepositoryClass');

        $classMetadata->isMappedSuperclass = true;

        $this->resourceSubscriber->loadClassMetadata($event);

        $this->assertTrue($classMetadata->isMappedSuperclass);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoadClassMetadataEventArgs
     */
    private function createLoadClassMetadataEventArgsMock()
    {
        return $this->getMockBuilder(LoadClassMetadataEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function createClassMetadataMock()
    {
        return $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
