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

use Lug\Component\Grid\Model\Builder\FilterBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->builder = new FilterBuilder();
    }

    public function testBuild()
    {
        $filter = $this->builder->build([
            'name' => $name = 'my.name',
            'type' => $type = 'my.type',
        ]);

        $this->assertSame($name, $filter->getName());
        $this->assertSame($name, $filter->getLabel());
        $this->assertSame($type, $filter->getType());
        $this->assertFalse($filter->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $filter = $this->builder->build([
            'name'    => $name = 'my.name',
            'label'   => $label = 'my.label',
            'type'    => $type = 'my.type',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $filter->getName());
        $this->assertSame($label, $filter->getLabel());
        $this->assertSame($type, $filter->getType());
        $this->assertSame($options, $filter->getOptions());
    }

    public function testBuildResource()
    {
        $filter = $this->builder->build([
            'name'    => $name = 'my.name',
            'type'    => $type = 'resource',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $filter->getName());
        $this->assertSame($name, $filter->getLabel());
        $this->assertSame($type, $filter->getType());
        $this->assertSame(array_merge($options, ['resource' => $name]), $filter->getOptions());
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The filter config "name" could not be found.
     */
    public function testBuildWithMissingName()
    {
        $this->builder->build(['type' => 'my.type']);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The filter config "type" could not be found.
     */
    public function testBuildWithMissingType()
    {
        $this->builder->build(['name' => 'my.name']);
    }
}
