<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Sort\Type;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Sort\Type\ColumnType;
use Lug\Component\Grid\Sort\Type\TypeInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new ColumnType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    public function testSort()
    {
        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->once())
            ->method('getProperty')
            ->with($this->identicalTo($field = 'field'))
            ->will($this->returnValue($property = 'property'));

        $builder
            ->expects($this->once())
            ->method('orderBy')
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($data = 'data')
            );

        $this->type->sort($data, ['builder' => $builder, 'field' => $field]);
    }

    public function testName()
    {
        $this->assertSame('column', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->getMock(DataSourceBuilderInterface::class);
    }
}
