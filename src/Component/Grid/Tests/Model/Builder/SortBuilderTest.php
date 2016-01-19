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

use Lug\Component\Grid\Model\Builder\SortBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SortBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SortBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->builder = new SortBuilder();
    }

    public function testBuild()
    {
        $sort = $this->builder->build([
            'name' => $name = 'my.name',
            'type' => $type = 'my.type',
        ]);

        $this->assertSame($name, $sort->getName());
        $this->assertSame($type, $sort->getType());
        $this->assertFalse($sort->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $sort = $this->builder->build([
            'name'    => $name = 'my.name',
            'type'    => $type = 'my.type',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $sort->getName());
        $this->assertSame($type, $sort->getType());
        $this->assertSame($options, $sort->getOptions());
    }

    public function testBuildResource()
    {
        $sort = $this->builder->build([
            'name'    => $name = 'my.name',
            'type'    => $type = 'resource',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $sort->getName());
        $this->assertSame($type, $sort->getType());
        $this->assertSame(array_merge($options, ['resource' => $name]), $sort->getOptions());
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The sort config "name" could not be found.
     */
    public function testBuildWithMissingName()
    {
        $this->builder->build(['type' => 'my.type']);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The sort config "type" could not be found.
     */
    public function testBuildWithMissingType()
    {
        $this->builder->build(['name' => 'my.name']);
    }
}
