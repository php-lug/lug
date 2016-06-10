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

use Lug\Component\Grid\Column\Type\TypeInterface;
use Lug\Component\Grid\Registry\ColumnRegistry;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnRegistry
     */
    private $columnRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->columnRegistry = new ColumnRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->columnRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->columnRegistry));
    }

    public function testInitialState()
    {
        $this->columnRegistry = new ColumnRegistry([$key = 'foo' => $value = $this->createColumnMock()]);

        $this->assertSame($value, $this->columnRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(TypeInterface::class);
    }
}
