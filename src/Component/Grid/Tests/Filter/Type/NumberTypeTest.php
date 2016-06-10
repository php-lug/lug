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
use Lug\Component\Grid\Filter\Type\NumberType;
use Lug\Component\Grid\Model\FilterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class NumberTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NumberType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new NumberType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    /**
     * @dataProvider simpleFilterProvider
     */
    public function testSimpleFilter($type, $method)
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
                $this->identicalTo($data = 123.4)
            )
            ->will($this->returnValue($placeholder = 'placeholder'));

        $expressionBuilder
            ->expects($this->once())
            ->method($method)
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

        $this->type->filter(['type' => $type, 'value' => $data], [
            'builder'           => $builder,
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $this->createFilterMock(),
            'fields'            => [$field],
        ]);
    }

    /**
     * @dataProvider compoundFilterProvider
     */
    public function testCompoundFilter($type, $conditionMethod, $fromMethod, $toMethod)
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
            ->expects($this->exactly(2))
            ->method('createPlaceholder')
            ->will($this->returnValueMap([
                [$field, $fromData = 123.4, null, $fromPlaceholder = 'from_placeholder'],
                [$field, $toData = 432.1, null, $toPlaceholder = 'to_placeholder'],
            ]));

        $expressionBuilder
            ->expects($this->once())
            ->method($fromMethod)
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($fromPlaceholder)
            )
            ->will($this->returnValue($fromExpression = 'from_expression'));

        $expressionBuilder
            ->expects($this->once())
            ->method($toMethod)
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($toPlaceholder)
            )
            ->will($this->returnValue($toExpression = 'to_expression'));

        $expression = 'expression';

        if ($conditionMethod !== 'orX') {
            $expressionBuilder
                ->expects($this->once())
                ->method($conditionMethod)
                ->with($this->identicalTo([$fromExpression, $toExpression]))
                ->will($this->returnValue($expression));

            $expressionBuilder
                ->expects($this->once())
                ->method('orX')
                ->with($this->identicalTo([$expression]))
                ->will($this->returnValue($expression));
        } else {
            $expressionBuilder
                ->expects($this->exactly(2))
                ->method('orX')
                ->will($this->returnValueMap([
                    [[$fromExpression, $toExpression], $expression],
                    [[$expression], $expression],
                ]));
        }

        $builder
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->identicalTo($expression));

        $this->type->filter(['type' => $type, 'from' => $fromData, 'to' => $toData], [
            'builder'           => $builder,
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $this->createFilterMock(),
            'fields'            => [$field],
        ]);
    }

    /**
     * @dataProvider emptyFilterProvider
     */
    public function testEmptyFilter($type, $method)
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

        $expressionBuilder
            ->expects($this->once())
            ->method($method)
            ->with($this->identicalTo($property))
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

        $this->type->filter(['type' => $type], [
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
        $this->assertSame('number', $this->type->getName());
    }

    public function testTypes()
    {
        $this->assertSame([
            NumberType::TYPE_GREATER_THAN_OR_EQUALS,
            NumberType::TYPE_GREATER_THAN,
            NumberType::TYPE_LESS_THAN_OR_EQUALS,
            NumberType::TYPE_LESS_THAN,
            NumberType::TYPE_EQUALS,
            NumberType::TYPE_NOT_EQUALS,
            NumberType::TYPE_BETWEEN,
            NumberType::TYPE_NOT_BETWEEN,
            NumberType::TYPE_EMPTY,
            NumberType::TYPE_NOT_EMPTY,
        ], NumberType::getTypes());
    }

    public function testSimpleTypes()
    {
        $this->assertSame([
            NumberType::TYPE_GREATER_THAN_OR_EQUALS,
            NumberType::TYPE_GREATER_THAN,
            NumberType::TYPE_LESS_THAN_OR_EQUALS,
            NumberType::TYPE_LESS_THAN,
            NumberType::TYPE_EQUALS,
            NumberType::TYPE_NOT_EQUALS,
        ], NumberType::getSimpleTypes());
    }

    public function testCompoundTypes()
    {
        $this->assertSame([
            NumberType::TYPE_BETWEEN,
            NumberType::TYPE_NOT_BETWEEN,
        ], NumberType::getCompoundTypes());
    }

    public function testEmptyTypes()
    {
        $this->assertSame([
            NumberType::TYPE_EMPTY,
            NumberType::TYPE_NOT_EMPTY,
        ], NumberType::getEmptyTypes());
    }

    /**
     * @return mixed[]
     */
    public function simpleFilterProvider()
    {
        return [
            'greater_than_or_equals' => [NumberType::TYPE_GREATER_THAN_OR_EQUALS, 'gte'],
            'greater_than'           => [NumberType::TYPE_GREATER_THAN, 'gt'],
            'less_than_or_equals'    => [NumberType::TYPE_LESS_THAN_OR_EQUALS, 'lte'],
            'less_than'              => [NumberType::TYPE_LESS_THAN, 'lt'],
            'equals'                 => [NumberType::TYPE_EQUALS, 'eq'],
            'not_equals'             => [NumberType::TYPE_NOT_EQUALS, 'neq'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function compoundFilterProvider()
    {
        return [
            'between'     => [NumberType::TYPE_BETWEEN, 'andX', 'gte', 'lte'],
            'not_between' => [NumberType::TYPE_NOT_BETWEEN, 'orX', 'lte', 'gte'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function emptyFilterProvider()
    {
        return [
            'empty'     => [NumberType::TYPE_EMPTY, 'isNull'],
            'not_empty' => [NumberType::TYPE_NOT_EMPTY, 'isNotNull'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function invalidFilterProvider()
    {
        return [
            'null'          => [null],
            'boolean'       => [true],
            'string'        => ['foo'],
            'number'        => [123.4],
            'array'         => [[]],
            'object'        => [new \stdClass()],
            'invalid_type'  => [['type' => 'foo']],
            'missing_value' => [['type' => NumberType::TYPE_EQUALS]],
            'invalid_value' => [['type' => NumberType::TYPE_EQUALS, 'value' => 'foo']],
            'missing_from'  => [['type' => NumberType::TYPE_BETWEEN, 'to' => $number = 123.4]],
            'invalid_from'  => [['type' => NumberType::TYPE_BETWEEN, 'to' => $number, 'from' => 'foo']],
            'missing_to'    => [['type' => NumberType::TYPE_BETWEEN, 'from' => $number]],
            'invalid_to'    => [['type' => NumberType::TYPE_BETWEEN, 'from' => $number, 'to' => 'foo']],
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
