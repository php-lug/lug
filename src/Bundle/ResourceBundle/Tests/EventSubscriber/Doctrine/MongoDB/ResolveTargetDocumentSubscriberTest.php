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

use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Tools\ResolveTargetDocumentListener;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB\ResolveTargetDocumentSubscriber;
use Lug\Bundle\ResourceBundle\EventSubscriber\ResolveTargetSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetDocumentSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResolveTargetDocumentSubscriber
     */
    private $resolveTargetDocumentSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists(ResolveTargetDocumentListener::class)) {
            $this->markTestSkipped();
        }

        $this->resolveTargetDocumentSubscriber = new ResolveTargetDocumentSubscriber();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ResolveTargetSubscriberInterface::class, $this->resolveTargetDocumentSubscriber);
        $this->assertInstanceOf(ResolveTargetDocumentListener::class, $this->resolveTargetDocumentSubscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([Events::loadClassMetadata], $this->resolveTargetDocumentSubscriber->getSubscribedEvents());
    }

    public function testLoadClassMetadata()
    {
        $event = $this->createLoadClassMetadataEventArgsMock();
        $event
            ->expects($this->exactly(2))
            ->method('getClassMetadata')
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $classMetadata
            ->expects($this->once())
            ->method('setDiscriminatorMap')
            ->with($this->identicalTo([$discriminator = 'foo' => $newEntity = 'baz']));

        $classMetadata->discriminatorMap = [$discriminator => $originalEntity = 'bar'];

        $this->resolveTargetDocumentSubscriber->addResolveTarget($originalEntity, $newEntity);
        $this->resolveTargetDocumentSubscriber->loadClassMetadata($event);
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
}
