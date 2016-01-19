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

use Lug\Component\Grid\Model\Filter;
use Lug\Component\Grid\Model\FilterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filter
     */
    private $filter;

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

        $this->filter = new Filter($this->name, $this->label, $this->type, $this->options);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testDefaultState()
    {
        $this->filter = new Filter($this->name, $this->label, $this->type);

        $this->assertSame($this->name, $this->filter->getName());
        $this->assertSame($this->label, $this->filter->getLabel());
        $this->assertSame($this->type, $this->filter->getType());
        $this->assertFalse($this->filter->hasOptions());
        $this->assertEmpty($this->filter->getOptions());
        $this->assertFalse($this->filter->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->assertSame($this->name, $this->filter->getName());
        $this->assertSame($this->label, $this->filter->getLabel());
        $this->assertSame($this->type, $this->filter->getType());

        $this->assertTrue($this->filter->hasOptions());
        $this->assertSame($this->options, $this->filter->getOptions());
        $this->assertTrue($this->filter->hasOption('foo'));
        $this->assertSame('bar', $this->filter->getOption('foo'));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The filter option "bar" could not be found.
     */
    public function testMissingOption()
    {
        $this->filter->getOption('bar');
    }
}
