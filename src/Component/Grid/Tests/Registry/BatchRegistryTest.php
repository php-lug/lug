<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Registry;

use Lug\Component\Grid\Batch\Type\TypeInterface;
use Lug\Component\Grid\Registry\BatchRegistry;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BatchRegistry
     */
    private $batchRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->batchRegistry = new BatchRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->batchRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->batchRegistry));
    }

    public function testInitialState()
    {
        $this->batchRegistry = new BatchRegistry([$key = 'foo' => $value = $this->createBatchMock()]);

        $this->assertSame($value, $this->batchRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createBatchMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
