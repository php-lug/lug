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

use Lug\Component\Grid\Model\Builder\ColumnBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->builder = new ColumnBuilder();
    }

    public function testBuild()
    {
        $column = $this->builder->build(['name' => $name = 'my.name']);

        $this->assertSame($name, $column->getName());
        $this->assertSame($name, $column->getLabel());
        $this->assertSame('text', $column->getType());
        $this->assertFalse($column->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $column = $this->builder->build([
            'name'    => $name = 'my.name',
            'label'   => $label = 'my.label',
            'type'    => $type = 'my.type',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $column->getName());
        $this->assertSame($label, $column->getLabel());
        $this->assertSame($type, $column->getType());
        $this->assertSame($options, $column->getOptions());
    }

    public function testBuildResource()
    {
        $column = $this->builder->build([
            'name'    => $name = 'my.name',
            'type'    => $type = 'resource',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $column->getName());
        $this->assertSame($name, $column->getLabel());
        $this->assertSame($type, $column->getType());
        $this->assertSame(array_merge($options, ['resource' => $name]), $column->getOptions());
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The column config "name" could not be found.
     */
    public function testBuildWithMissingName()
    {
        $this->builder->build([]);
    }
}
