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

use Lug\Component\Grid\Model\Builder\BatchBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BatchBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->builder = new BatchBuilder();
    }

    public function testBuild()
    {
        $batch = $this->builder->build([
            'name' => $name = 'my.name',
            'type' => $type = 'my.type',
        ]);

        $this->assertSame($name, $batch->getName());
        $this->assertSame($name, $batch->getLabel());
        $this->assertSame($type, $batch->getType());
        $this->assertFalse($batch->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $batch = $this->builder->build([
            'name'    => $name = 'my.name',
            'label'   => $label = 'my.label',
            'type'    => $type = 'my.type',
            'options' => $options = ['foo' => 'bar'],
        ]);

        $this->assertSame($name, $batch->getName());
        $this->assertSame($label, $batch->getLabel());
        $this->assertSame($type, $batch->getType());
        $this->assertSame($options, $batch->getOptions());
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The batch config "name" could not be found.
     */
    public function testBuildWithMissingName()
    {
        $this->builder->build(['type' => 'my.type']);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\ConfigNotFoundException
     * @expectedExceptionMessage The batch config "type" could not be found.
     */
    public function testBuildWithMissingType()
    {
        $this->builder->build(['name' => 'my.name']);
    }
}
