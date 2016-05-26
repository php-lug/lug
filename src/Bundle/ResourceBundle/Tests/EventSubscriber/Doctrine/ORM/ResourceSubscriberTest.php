<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\EventSubscriber\Doctrine\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM\ResourceSubscriber;
use Lug\Component\Registry\Model\RegistryInterface;
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
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $serviceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
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

    public function testLoadClassMetadataWithResourceAndNoParentClasses()
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

        $classMetadata->parentClasses = [];
        $classMetadata->isMappedSuperclass = false;

        $this->resourceSubscriber->loadClassMetadata($event);

        $this->assertFalse($classMetadata->isMappedSuperclass);
    }

    public function testLoadClassMetadataWithResourceAndParentClassesWithoutInheritance()
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

        $classMetadata->parentClasses = [$parentClass = 'ParentClass'];

        $event
            ->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager = $this->createObjectManagerMock()));

        $objectManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($parentClass))
            ->will($this->returnValue($parentMetadata = $this->createClassMetadataMock()));

        $parentMetadata->isMappedSuperclass = false;
        $parentMetadata->inheritanceType = ClassMetadata::INHERITANCE_TYPE_NONE;

        $resource
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository = 'repository'));

        $classMetadata
            ->expects($this->once())
            ->method('setCustomRepositoryClass')
            ->with($this->identicalTo($repository));

        $this->resourceSubscriber->loadClassMetadata($event);

        $this->assertFalse($classMetadata->isMappedSuperclass);
        $this->assertTrue($parentMetadata->isMappedSuperclass);
    }

    public function testLoadClassMetadataWithResourceAndParentClassesWithInheritance()
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

        $classMetadata->parentClasses = [$parentClass = 'ParentClass'];

        $event
            ->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager = $this->createObjectManagerMock()));

        $objectManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($parentClass))
            ->will($this->returnValue($parentMetadata = $this->createClassMetadataMock()));

        $parentMetadata->isMappedSuperclass = false;
        $parentMetadata->inheritanceType = ClassMetadata::INHERITANCE_TYPE_SINGLE_TABLE;

        $resource
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository = 'repository'));

        $classMetadata
            ->expects($this->once())
            ->method('setCustomRepositoryClass')
            ->with($this->identicalTo($repository));

        $this->resourceSubscriber->loadClassMetadata($event);

        $this->assertFalse($classMetadata->isMappedSuperclass);
        $this->assertFalse($parentMetadata->isMappedSuperclass);
    }

    public function testLoadClassMetadataWithResourceAndParentClassesWithoutClassMetadata()
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

        $classMetadata->parentClasses = [$parentClass = 'ParentClass'];

        $event
            ->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager = $this->createObjectManagerMock()));

        $objectManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($parentClass))
            ->will($this->throwException($this->createMappingExceptionMock()));

        $resource
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository = 'repository'));

        $classMetadata
            ->expects($this->once())
            ->method('setCustomRepositoryClass')
            ->with($this->identicalTo($repository));

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
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(RegistryInterface::class);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    private function createObjectManagerMock()
    {
        return $this->getMock(ObjectManager::class);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MappingException
     */
    private function createMappingExceptionMock()
    {
        return $this->getMock(MappingException::class);
    }
}
