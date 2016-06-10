<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\RegistryBundle\Tests\Model;

use Lug\Bundle\RegistryBundle\Model\LazyRegistry;
use Lug\Bundle\RegistryBundle\Model\LazyRegistryInterface;
use Lug\Component\Registry\Model\Registry;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LazyRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyRegistry
     */
    private $lazyRegistry;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var string[]
     */
    private $lazyServices;

    /**
     * @var ServiceInterface[]
     */
    private $containerServices;

    /**
     * @var ServiceInterface[]
     */
    private $services;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->services = ['foo' => $this->createServiceMock(), 'bar' => $this->createServiceMock()];
        $this->containerServices = ['my.baz' => $this->createServiceMock(), 'my.bat' => $this->createServiceMock()];
        $this->lazyServices = ['baz' => 'my.baz', 'bat' => 'my.bat'];
        $this->container = $this->createContainerMock();
        $this->registry = new Registry(ServiceInterface::class, $this->services);

        $this->lazyRegistry = new LazyRegistry(
            $this->container,
            $this->registry,
            $this->lazyServices
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegistryInterface::class, $this->lazyRegistry);
        $this->assertInstanceOf(LazyRegistryInterface::class, $this->lazyRegistry);
    }

    public function testDefaultState()
    {
        $this->lazyRegistry = new LazyRegistry(
            $this->container,
            new Registry(ServiceInterface::class)
        );

        $this->assertEmpty(iterator_to_array($this->lazyRegistry));
        $this->assertCount(0, $this->lazyRegistry);
    }

    public function testInitialState()
    {
        $services = array_merge(
            $this->services,
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        $this->assertSame($services, iterator_to_array($this->lazyRegistry));
        $this->assertCount(count($services), $this->lazyRegistry);
    }

    public function testHasLazy()
    {
        foreach (array_keys($this->lazyServices) as $type) {
            $this->assertTrue($this->lazyRegistry->hasLazy($type));
        }

        foreach (array_keys($this->services) as $type) {
            $this->assertFalse($this->lazyRegistry->hasLazy($type));
        }
    }

    public function testGetLazy()
    {
        foreach ($this->lazyServices as $type => $service) {
            $this->assertSame($service, $this->lazyRegistry->getLazy($type));
        }
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException
     * @expectedExceptionMessage The lazy service "foo" could not be found.
     */
    public function testGetLazyWithNonExistingLazy()
    {
        $this->lazyRegistry->getLazy('foo');
    }

    public function testSetLazy()
    {
        $this->containerServices[$containerService = 'my.ban'] = $service = $this->createServiceMock();

        $this->container = $this->createContainerMock();
        $this->registry = new Registry(ServiceInterface::class, $this->services);

        $this->lazyRegistry = new LazyRegistry(
            $this->container,
            $this->registry,
            $this->lazyServices
        );

        $this->lazyServices[$type = 'ban'] = $containerService;
        $this->lazyRegistry->setLazy($type, $containerService);

        $this->assertTrue($this->lazyRegistry->hasLazy($type));
        $this->assertSame($containerService, $this->lazyRegistry->getLazy($type));

        $services = array_merge(
            $this->services,
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        $this->assertSame($services, iterator_to_array($this->lazyRegistry));
        $this->assertCount(count($services), $this->lazyRegistry);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceAlreadyExistsException
     * @expectedExceptionMessage The lazy service "baz" already exists.
     */
    public function testSetLazyWithExistingLazy()
    {
        $this->lazyRegistry->setLazy('baz', 'my.baz');
    }

    public function testRemoveLazy()
    {
        $this->testInitialState();

        foreach (array_keys($this->lazyServices) as $type) {
            $this->lazyRegistry->removeLazy($type);
        }

        $this->assertSame($this->services, iterator_to_array($this->lazyRegistry));
        $this->assertCount(count($this->services), $this->lazyRegistry);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException
     * @expectedExceptionMessage The lazy service "ban" could not be found.
     */
    public function testRemoveLazyWithNonExistingLazy()
    {
        $this->lazyRegistry->removeLazy('ban');
    }

    public function testOffsetExists()
    {
        foreach (array_keys(array_merge($this->services, $this->lazyServices)) as $type) {
            $this->assertTrue(isset($this->lazyRegistry[$type]));
        }

        $this->assertFalse(isset($this->lazyRegistry['ban']));
    }

    public function testOffsetGet()
    {
        $services = array_merge(
            $this->services,
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        foreach ($services as $type => $service) {
            $this->assertSame($service, $this->lazyRegistry[$type]);
        }
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException
     * @expectedExceptionMessage The lazy service "ban" could not be found.
     */
    public function testOffsetGetWithNonExistingService()
    {
        $this->lazyRegistry['ban'];
    }

    public function testOffsetSet()
    {
        $this->lazyRegistry[$type = 'ban'] = $service = $this->createServiceMock();

        $this->assertSame($service, $this->lazyRegistry[$type]);

        $services = array_merge(
            $this->services,
            [$type => $service],
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        $this->assertSame($services, iterator_to_array($this->lazyRegistry));
        $this->assertCount(count($services), $this->lazyRegistry);
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceAlreadyExistsException
     * @expectedExceptionMessage The service "baz" already exists.
     */
    public function testOffsetSetWithExistingService()
    {
        $this->lazyRegistry['baz'] = $this->createServiceMock();
    }

    public function testOffsetUnset()
    {
        foreach (array_keys(array_merge($this->services, $this->lazyServices)) as $type) {
            unset($this->lazyRegistry[$type]);

            $this->assertFalse(isset($this->lazyRegistry[$type]));
        }

        $this->assertEmpty(iterator_to_array($this->lazyRegistry));
        $this->assertCount(0, $this->lazyRegistry);
    }

    /**
     * @return ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerMock()
    {
        $hasMap = [];
        $getMap = [];

        foreach ($this->containerServices as $id => $service) {
            $hasMap[] = [$id, true];
            $getMap[] = [$id, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $service];
        }

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($hasMap));

        $container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($getMap));

        return $container;
    }

    /**
     * @return ServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createServiceMock()
    {
        return $this->createMock(ServiceInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ServiceInterface
{
}
