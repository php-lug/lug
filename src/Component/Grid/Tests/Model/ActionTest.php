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

use Lug\Component\Grid\Model\Action;
use Lug\Component\Grid\Model\ActionInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Action
     */
    private $action;

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

        $this->action = new Action($this->name, $this->label, $this->type);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ActionInterface::class, $this->action);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->action->getName());
        $this->assertSame($this->label, $this->action->getLabel());
        $this->assertSame($this->type, $this->action->getType());
        $this->assertFalse($this->action->hasOptions());
        $this->assertEmpty($this->action->getOptions());
        $this->assertFalse($this->action->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->action = new Action(
            $this->name,
            $this->label,
            $this->type,
            $options = [$option = 'foo' => $value = 'bar']
        );

        $this->assertTrue($this->action->hasOptions());
        $this->assertSame($options, $this->action->getOptions());
        $this->assertTrue($this->action->hasOption($option));
        $this->assertSame($value, $this->action->getOption($option));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The action option "foo" could not be found.
     */
    public function testMissingOption()
    {
        $this->action->getOption('foo');
    }
}
