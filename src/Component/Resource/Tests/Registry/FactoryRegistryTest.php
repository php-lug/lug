<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Registry;

use Lug\Component\Registry\Model\Registry;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Registry\FactoryRegistry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FactoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FactoryRegistry
     */
    private $factoryRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->factoryRegistry = new FactoryRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegistryInterface::class, $this->factoryRegistry);
        $this->assertInstanceOf(Registry::class, $this->factoryRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->factoryRegistry));
    }

    public function testInitialState()
    {
        $this->factoryRegistry = new FactoryRegistry([$key = 'foo' => $value = $this->createFactoryMock()]);

        $this->assertSame($value, $this->factoryRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createFactoryMock()
    {
        return $this->getMock(FactoryInterface::class);
    }
}
