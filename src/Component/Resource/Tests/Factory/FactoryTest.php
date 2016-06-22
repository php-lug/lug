<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Factory;

use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->factory = new Factory($this->resource, $this->propertyAccessor);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testCreateWithOptions()
    {
        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model = Fixture::class));

        $fixture = $this->factory->create(['name' => $name = 'foo']);

        $this->assertInstanceOf($model, $fixture);
        $this->assertSame($name, $fixture->getName());
    }

    public function testCreateWithoutOptions()
    {
        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model = Fixture::class));

        $fixture = $this->factory->create();

        $this->assertInstanceOf($model, $fixture);
        $this->assertNull($fixture->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Fixture
{
    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
