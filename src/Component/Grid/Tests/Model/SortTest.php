<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Model;

use Lug\Component\Grid\Model\Sort;
use Lug\Component\Grid\Model\SortInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SortTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sort
     */
    private $sort;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed[]
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->name = 'name';
        $this->type = 'type';
        $this->options = ['foo' => 'bar'];

        $this->sort = new Sort($this->name, $this->type);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(SortInterface::class, $this->sort);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->sort->getName());
        $this->assertSame($this->type, $this->sort->getType());
        $this->assertFalse($this->sort->hasOptions());
        $this->assertEmpty($this->sort->getOptions());
        $this->assertFalse($this->sort->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->sort = new Sort($this->name, $this->type, $options = [$option = 'foo' => $value = 'bar']);

        $this->assertTrue($this->sort->hasOptions());
        $this->assertSame($options, $this->sort->getOptions());
        $this->assertTrue($this->sort->hasOption($option));
        $this->assertSame($value, $this->sort->getOption($option));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The sort option "foo" could not be found.
     */
    public function testMissingOption()
    {
        $this->sort->getOption('foo');
    }
}
