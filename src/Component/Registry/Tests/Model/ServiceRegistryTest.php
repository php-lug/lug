<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Registry\Tests\Model;

use Lug\Component\Registry\Model\ServiceRegistry;
use Lug\Component\Registry\Model\ServiceRegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ServiceRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceRegistry
     */
    private $serviceRegistry;

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
        $this->serviceRegistry = new ServiceRegistry(ServiceInterface::class, $this->services);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ServiceRegistryInterface::class, $this->serviceRegistry);
        $this->assertInstanceOf(\ArrayAccess::class, $this->serviceRegistry);
        $this->assertInstanceOf(\Countable::class, $this->serviceRegistry);
        $this->assertInstanceOf(\IteratorAggregate::class, $this->serviceRegistry);
    }

    public function testDefaultState()
    {
        $this->serviceRegistry = new ServiceRegistry(ServiceInterface::class);

        $this->assertEmpty(iterator_to_array($this->serviceRegistry));
        $this->assertCount(0, $this->serviceRegistry);
    }

    public function testInitialState()
    {
        $this->assertSame($this->services, iterator_to_array($this->serviceRegistry));
        $this->assertCount(count($this->services), $this->serviceRegistry);
    }

    public function testOffsetExists()
    {
        foreach (array_keys($this->services) as $type) {
            $this->assertTrue(isset($this->serviceRegistry[$type]));
        }

        $this->assertFalse(isset($this->serviceRegistry['baz']));
    }

    public function testOffsetGet()
    {
        foreach ($this->services as $type => $service) {
            $this->assertSame($service, $this->serviceRegistry[$type]);
        }
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceNotFoundException
     * @expectedExceptionMessage The service "baz" could not be found.
     */
    public function testOffsetGetWithNonExistingService()
    {
        $this->serviceRegistry['baz'];
    }

    public function testOffsetSet()
    {
        $this->serviceRegistry[$type = 'baz'] = $service = $this->createServiceMock();

        $this->assertSame($service, $this->serviceRegistry[$type]);
        $this->assertSame(array_merge($this->services, [$type => $service]), iterator_to_array($this->serviceRegistry));
        $this->assertCount(count($this->services) + 1, $this->serviceRegistry);
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceAlreadyExistsException
     * @expectedExceptionMessage The service "foo" already exists.
     */
    public function testOffsetSetWithExistingService()
    {
        $this->serviceRegistry['foo'] = $this->createServiceMock();
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\InvalidServiceException
     * @expectedExceptionMessage The service for the registry "Lug\Component\Registry\Model\ServiceRegistry" must be an instance of "Lug\Component\Registry\Tests\Model\ServiceInterface", got "string".
     */
    public function testOffsetSetWithScalarService()
    {
        $this->serviceRegistry['baz'] = 'baz';
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\InvalidServiceException
     * @expectedExceptionMessage The service for the registry "Lug\Component\Registry\Model\ServiceRegistry" must be an instance of "Lug\Component\Registry\Tests\Model\ServiceInterface", got "stdClass".
     */
    public function testOffsetSetWithInvalidService()
    {
        $this->serviceRegistry['baz'] = new \stdClass();
    }

    public function testOffsetUnset()
    {
        foreach (array_keys($this->services) as $type) {
            unset($this->serviceRegistry[$type]);

            $this->assertFalse(isset($this->serviceRegistry[$type]));
        }

        $this->assertEmpty(iterator_to_array($this->serviceRegistry));
        $this->assertCount(0, $this->serviceRegistry);
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceNotFoundException
     * @expectedExceptionMessage The service "baz" could not be found.
     */
    public function testOffsetUnsetWithNonExistingService()
    {
        unset($this->serviceRegistry['baz']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceInterface
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
