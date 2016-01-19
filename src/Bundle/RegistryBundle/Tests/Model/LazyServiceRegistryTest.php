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

use Lug\Bundle\RegistryBundle\Model\LazyServiceRegistry;
use Lug\Bundle\RegistryBundle\Model\LazyServiceRegistryInterface;
use Lug\Component\Registry\Model\ServiceRegistry;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LazyServiceRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyServiceRegistry
     */
    private $lazyServiceRegistry;

    /**
     * @var ServiceRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceRegistry;

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
        $this->serviceRegistry = new ServiceRegistry(ServiceInterface::class, $this->services);

        $this->lazyServiceRegistry = new LazyServiceRegistry(
            $this->container,
            $this->serviceRegistry,
            $this->lazyServices
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ServiceRegistryInterface::class, $this->lazyServiceRegistry);
        $this->assertInstanceOf(LazyServiceRegistryInterface::class, $this->lazyServiceRegistry);
    }

    public function testDefaultState()
    {
        $this->lazyServiceRegistry = new LazyServiceRegistry(
            $this->container,
            new ServiceRegistry(ServiceInterface::class)
        );

        $this->assertEmpty(iterator_to_array($this->lazyServiceRegistry));
        $this->assertCount(0, $this->lazyServiceRegistry);
    }

    public function testInitialState()
    {
        $services = array_merge(
            $this->services,
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        $this->assertSame($services, iterator_to_array($this->lazyServiceRegistry));
        $this->assertCount(count($services), $this->lazyServiceRegistry);
    }

    public function testHasLazy()
    {
        foreach (array_keys($this->lazyServices) as $type) {
            $this->assertTrue($this->lazyServiceRegistry->hasLazy($type));
        }

        foreach (array_keys($this->services) as $type) {
            $this->assertFalse($this->lazyServiceRegistry->hasLazy($type));
        }
    }

    public function testGetLazy()
    {
        foreach ($this->lazyServices as $type => $service) {
            $this->assertSame($service, $this->lazyServiceRegistry->getLazy($type));
        }
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException
     * @expectedExceptionMessage The lazy service "foo" could not be found.
     */
    public function testGetLazyWithNonExistingLazy()
    {
        $this->lazyServiceRegistry->getLazy('foo');
    }

    public function testSetLazy()
    {
        $this->containerServices[$containerService = 'my.ban'] = $service = $this->createServiceMock();

        $this->container = $this->createContainerMock();
        $this->serviceRegistry = new ServiceRegistry(ServiceInterface::class, $this->services);

        $this->lazyServiceRegistry = new LazyServiceRegistry(
            $this->container,
            $this->serviceRegistry,
            $this->lazyServices
        );

        $this->lazyServices[$type = 'ban'] = $containerService;
        $this->lazyServiceRegistry->setLazy($type, $containerService);

        $this->assertTrue($this->lazyServiceRegistry->hasLazy($type));
        $this->assertSame($containerService, $this->lazyServiceRegistry->getLazy($type));

        $services = array_merge(
            $this->services,
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        $this->assertSame($services, iterator_to_array($this->lazyServiceRegistry));
        $this->assertCount(count($services), $this->lazyServiceRegistry);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceAlreadyExistsException
     * @expectedExceptionMessage The lazy service "baz" already exists.
     */
    public function testSetLazyWithExistingLazy()
    {
        $this->lazyServiceRegistry->setLazy('baz', 'my.baz');
    }

    public function testRemoveLazy()
    {
        $this->testInitialState();

        foreach (array_keys($this->lazyServices) as $type) {
            $this->lazyServiceRegistry->removeLazy($type);
        }

        $this->assertSame($this->services, iterator_to_array($this->lazyServiceRegistry));
        $this->assertCount(count($this->services), $this->lazyServiceRegistry);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException
     * @expectedExceptionMessage The lazy service "ban" could not be found.
     */
    public function testRemoveLazyWithNonExistingLazy()
    {
        $this->lazyServiceRegistry->removeLazy('ban');
    }

    public function testOffsetExists()
    {
        foreach (array_keys(array_merge($this->services, $this->lazyServices)) as $type) {
            $this->assertTrue(isset($this->lazyServiceRegistry[$type]));
        }

        $this->assertFalse(isset($this->lazyServiceRegistry['ban']));
    }

    public function testOffsetGet()
    {
        $services = array_merge(
            $this->services,
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        foreach ($services as $type => $service) {
            $this->assertSame($service, $this->lazyServiceRegistry[$type]);
        }
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException
     * @expectedExceptionMessage The lazy service "ban" could not be found.
     */
    public function testOffsetGetWithNonExistingService()
    {
        $this->lazyServiceRegistry['ban'];
    }

    public function testOffsetSet()
    {
        $this->lazyServiceRegistry[$type = 'ban'] = $service = $this->createServiceMock();

        $this->assertSame($service, $this->lazyServiceRegistry[$type]);

        $services = array_merge(
            $this->services,
            [$type => $service],
            array_combine(array_flip($this->lazyServices), $this->containerServices)
        );

        $this->assertSame($services, iterator_to_array($this->lazyServiceRegistry));
        $this->assertCount(count($services), $this->lazyServiceRegistry);
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceAlreadyExistsException
     * @expectedExceptionMessage The service "baz" already exists.
     */
    public function testOffsetSetWithExistingService()
    {
        $this->lazyServiceRegistry['baz'] = $this->createServiceMock();
    }

    public function testOffsetUnset()
    {
        foreach (array_keys(array_merge($this->services, $this->lazyServices)) as $type) {
            unset($this->lazyServiceRegistry[$type]);

            $this->assertFalse(isset($this->lazyServiceRegistry[$type]));
        }

        $this->assertEmpty(iterator_to_array($this->lazyServiceRegistry));
        $this->assertCount(0, $this->lazyServiceRegistry);
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

        $container = $this->getMock(ContainerInterface::class);
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
        return $this->getMock(ServiceInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ServiceInterface
{
}
