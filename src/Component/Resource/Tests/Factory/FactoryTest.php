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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->factory = new Factory($this->resource);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testCreate()
    {
        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model = \stdClass::class));

        $this->assertInstanceOf($model, $this->factory->create());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
