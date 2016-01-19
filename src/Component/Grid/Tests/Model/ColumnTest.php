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

use Lug\Component\Grid\Model\Column;
use Lug\Component\Grid\Model\ColumnInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Column
     */
    private $column;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

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
        $this->label = 'label';
        $this->type = 'type';
        $this->options = ['foo' => 'bar'];

        $this->column = new Column($this->name, $this->label, $this->type);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ColumnInterface::class, $this->column);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->column->getName());
        $this->assertSame($this->label, $this->column->getLabel());
        $this->assertSame($this->type, $this->column->getType());
        $this->assertFalse($this->column->hasOptions());
        $this->assertEmpty($this->column->getOptions());
        $this->assertFalse($this->column->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->column = new Column(
            $this->name,
            $this->label,
            $this->type,
            $options = [$option = 'foo' => $value = 'bar']
        );

        $this->assertTrue($this->column->hasOptions());
        $this->assertSame($options, $this->column->getOptions());
        $this->assertTrue($this->column->hasOption($option));
        $this->assertSame($value, $this->column->getOption($option));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The column option "foo" could not be found.
     */
    public function testMissingOption()
    {
        $this->column->getOption('foo');
    }
}
