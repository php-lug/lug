<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Model\Builder;

use Lug\Component\Grid\Model\Builder\ActionBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->builder = new ActionBuilder();
    }

    public function testBuild()
    {
        $action = $this->builder->build([
            'name' => $name = 'my.name',
            'type' => $type = 'my.type',
        ]);

        $this->assertSame($name, $action->getName());
        $this->assertSame($name, $action->getLabel());
        $this->assertSame($type, $action->getType());
        $this->assertFalse($action->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $action = $this->builder->build([
            'name'    => $name = 'my.name',
            'label'   => $label = 'my.label',
            'type'    => $type = 'my.type',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $action->getName());
        $this->assertSame($label, $action->getLabel());
        $this->assertSame($type, $action->getType());
        $this->assertSame($options, $action->getOptions());
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The action config "name" could not be found.
     */
    public function testBuildWithMissingName()
    {
        $this->builder->build(['type' => 'my.type']);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The action config "type" could not be found.
     */
    public function testBuildWithMissingType()
    {
        $this->builder->build(['name' => 'my.name']);
    }
}
