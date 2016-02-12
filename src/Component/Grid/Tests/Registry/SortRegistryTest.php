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

use Lug\Component\Grid\Registry\SortRegistry;
use Lug\Component\Grid\Sort\Type\TypeInterface;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SortRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SortRegistry
     */
    private $sortRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->sortRegistry = new SortRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->sortRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->sortRegistry));
    }

    public function testInitialState()
    {
        $this->sortRegistry = new SortRegistry([$key = 'foo' => $value = $this->createSortMock()]);

        $this->assertSame($value, $this->sortRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createSortMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
