<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Filter\Type;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\DataSource\ExpressionBuilderInterface;
use Lug\Component\Grid\Filter\Type\AbstractType;
use Lug\Component\Grid\Filter\Type\BooleanType;
use Lug\Component\Grid\Model\FilterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BooleanType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new BooleanType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    /**
     * @dataProvider filterProvider
     */
    public function testFilter($data)
    {
        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->exactly(2))
            ->method('getExpressionBuilder')
            ->will($this->returnValue($expressionBuilder = $this->createExpressionBuilderMock()));

        $builder
            ->expects($this->once())
            ->method('getProperty')
            ->with($this->identicalTo($field = 'field'))
            ->will($this->returnValue($property = 'property'));

        $builder
            ->expects($this->once())
            ->method('createPlaceholder')
            ->with(
                $this->identicalTo($field),
                $this->identicalTo($data)
            )
            ->will($this->returnValue($placeholder = 'placeholder'));

        $expressionBuilder
            ->expects($this->once())
            ->method('eq')
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($placeholder)
            )
            ->will($this->returnValue($expression = 'expression'));

        $expressionBuilder
            ->expects($this->once())
            ->method('orX')
            ->with($this->identicalTo([$expression]))
            ->will($this->returnValue($expression));

        $builder
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->identicalTo($expression));

        $this->type->filter($data, [
            'builder'           => $builder,
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $this->createFilterMock(),
            'fields'            => [$field],
        ]);
    }

    /**
     * @dataProvider invalidFilterProvider
     */
    public function testInvalidFilter($data)
    {
        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->never())
            ->method('andWhere');

        $builder
            ->expects($this->never())
            ->method('orWhere');

        $this->type->filter($data, [
            'builder'           => $builder,
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $this->createFilterMock(),
            'fields'            => ['field'],
        ]);
    }

    public function testName()
    {
        $this->assertSame('boolean', $this->type->getName());
    }

    /**
     * @return mixed[]
     */
    public function filterProvider()
    {
        return [
            'true'  => [true],
            'false' => [false],
        ];
    }

    /**
     * @return mixed[]
     */
    public function invalidFilterProvider()
    {
        return [
            'null'   => [null],
            'string' => ['foo'],
            'number' => [123.4],
            'array'  => [[]],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ExpressionBuilderInterface
     */
    private function createExpressionBuilderMock()
    {
        return $this->createMock(ExpressionBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterInterface
     */
    private function createFilterMock()
    {
        return $this->createMock(FilterInterface::class);
    }
}
