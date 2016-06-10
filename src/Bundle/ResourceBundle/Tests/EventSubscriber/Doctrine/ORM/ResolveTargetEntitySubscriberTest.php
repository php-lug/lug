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

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM\ResolveTargetEntitySubscriber;
use Lug\Bundle\ResourceBundle\EventSubscriber\ResolveTargetSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetEntitySubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResolveTargetEntitySubscriber
     */
    private $resolveTargetEntitySubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resolveTargetEntitySubscriber = new ResolveTargetEntitySubscriber();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ResolveTargetSubscriberInterface::class, $this->resolveTargetEntitySubscriber);
        $this->assertInstanceOf(ResolveTargetEntityListener::class, $this->resolveTargetEntitySubscriber);
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

        $this->resolveTargetEntitySubscriber->addResolveTarget($originalEntity, $newEntity);
        $this->resolveTargetEntitySubscriber->loadClassMetadata($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoadClassMetadataEventArgs
     */
    private function createLoadClassMetadataEventArgsMock()
    {
        return $this->createMock(LoadClassMetadataEventArgs::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function createClassMetadataMock()
    {
        return $this->createMock(ClassMetadata::class);
    }
}
