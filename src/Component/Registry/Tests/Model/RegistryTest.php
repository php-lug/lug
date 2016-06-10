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

use Lug\Component\Registry\Model\Registry;
use Lug\Component\Registry\Model\RegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    private $registry;

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
        $this->registry = new Registry(ServiceInterface::class, $this->services);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegistryInterface::class, $this->registry);
        $this->assertInstanceOf(\ArrayAccess::class, $this->registry);
        $this->assertInstanceOf(\Countable::class, $this->registry);
        $this->assertInstanceOf(\IteratorAggregate::class, $this->registry);
    }

    public function testDefaultState()
    {
        $this->registry = new Registry(ServiceInterface::class);

        $this->assertEmpty(iterator_to_array($this->registry));
        $this->assertCount(0, $this->registry);
    }

    public function testInitialState()
    {
        $this->assertSame($this->services, iterator_to_array($this->registry));
        $this->assertCount(count($this->services), $this->registry);
    }

    public function testOffsetExists()
    {
        foreach (array_keys($this->services) as $type) {
            $this->assertTrue(isset($this->registry[$type]));
        }

        $this->assertFalse(isset($this->registry['baz']));
    }

    public function testOffsetGet()
    {
        foreach ($this->services as $type => $service) {
            $this->assertSame($service, $this->registry[$type]);
        }
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceNotFoundException
     * @expectedExceptionMessage The service "baz" could not be found.
     */
    public function testOffsetGetWithNonExistingService()
    {
        $this->registry['baz'];
    }

    public function testOffsetSet()
    {
        $this->registry[$type = 'baz'] = $service = $this->createServiceMock();

        $this->assertSame($service, $this->registry[$type]);
        $this->assertSame(array_merge($this->services, [$type => $service]), iterator_to_array($this->registry));
        $this->assertCount(count($this->services) + 1, $this->registry);
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceAlreadyExistsException
     * @expectedExceptionMessage The service "foo" already exists.
     */
    public function testOffsetSetWithExistingService()
    {
        $this->registry['foo'] = $this->createServiceMock();
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\InvalidServiceException
     * @expectedExceptionMessage The service for the registry "Lug\Component\Registry\Model\Registry" must be an instance of "Lug\Component\Registry\Tests\Model\ServiceInterface", got "string".
     */
    public function testOffsetSetWithScalarService()
    {
        $this->registry['baz'] = 'baz';
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\InvalidServiceException
     * @expectedExceptionMessage The service for the registry "Lug\Component\Registry\Model\Registry" must be an instance of "Lug\Component\Registry\Tests\Model\ServiceInterface", got "stdClass".
     */
    public function testOffsetSetWithInvalidService()
    {
        $this->registry['baz'] = new \stdClass();
    }

    public function testOffsetUnset()
    {
        foreach (array_keys($this->services) as $type) {
            unset($this->registry[$type]);

            $this->assertFalse(isset($this->registry[$type]));
        }

        $this->assertEmpty(iterator_to_array($this->registry));
        $this->assertCount(0, $this->registry);
    }

    /**
     * @expectedException \Lug\Component\Registry\Exception\ServiceNotFoundException
     * @expectedExceptionMessage The service "baz" could not be found.
     */
    public function testOffsetUnsetWithNonExistingService()
    {
        unset($this->registry['baz']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceInterface
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
