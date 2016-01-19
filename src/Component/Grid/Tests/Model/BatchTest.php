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

use Lug\Component\Grid\Model\Batch;
use Lug\Component\Grid\Model\BatchInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Batch
     */
    private $batch;

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

        $this->batch = new Batch($this->name, $this->label, $this->type);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BatchInterface::class, $this->batch);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->batch->getName());
        $this->assertSame($this->label, $this->batch->getLabel());
        $this->assertSame($this->type, $this->batch->getType());
        $this->assertFalse($this->batch->hasOptions());
        $this->assertEmpty($this->batch->getOptions());
        $this->assertFalse($this->batch->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->batch = new Batch(
            $this->name,
            $this->label,
            $this->type,
            $options = [$option = 'foo' => $value = 'bar']
        );

        $this->assertTrue($this->batch->hasOptions());
        $this->assertSame($options, $this->batch->getOptions());
        $this->assertTrue($this->batch->hasOption($option));
        $this->assertSame($value, $this->batch->getOption($option));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The batch option "foo" could not be found.
     */
    public function testMissingOption()
    {
        $this->batch->getOption('foo');
    }
}
