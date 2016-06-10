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
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Registry\ResourceRegistry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceRegistry
     */
    private $resourceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resourceRegistry = new ResourceRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegistryInterface::class, $this->resourceRegistry);
        $this->assertInstanceOf(Registry::class, $this->resourceRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->resourceRegistry));
    }

    public function testInitialState()
    {
        $this->resourceRegistry = new ResourceRegistry([$key = 'foo' => $value = $this->createResourceMock()]);

        $this->assertSame($value, $this->resourceRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
