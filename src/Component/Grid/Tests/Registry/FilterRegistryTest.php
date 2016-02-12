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

use Lug\Component\Grid\Filter\Type\TypeInterface;
use Lug\Component\Grid\Registry\FilterRegistry;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterRegistry
     */
    private $filterRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filterRegistry = new FilterRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->filterRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->filterRegistry));
    }

    public function testInitialState()
    {
        $this->filterRegistry = new FilterRegistry([$key = 'foo' => $value = $this->createFilterMock()]);

        $this->assertSame($value, $this->filterRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createFilterMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
