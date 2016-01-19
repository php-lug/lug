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
use Lug\Component\Grid\Filter\Type\DateTimeType;
use Lug\Component\Grid\Model\FilterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new DateTimeType();
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
                $this->identicalTo($data = new \DateTime())
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
    public function testCompoundFilter($type, $not = false)
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
                [$field, $fromData = new \DateTime(), null, $fromPlaceholder = 'from_placeholder'],
                [$field, $toData = new \DateTime(), null, $toPlaceholder = 'to_placeholder'],
            ]));

        $expressionBuilder
            ->expects($this->once())
            ->method($not ? 'notBetween' : 'between')
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($fromPlaceholder),
                $this->identicalTo($toPlaceholder)
            )
            ->will($this->returnValue($betweenExpression = $expression = 'between_expression'));

        $expressionBuilder
            ->expects($this->once())
            ->method('orX')
            ->with($this->identicalTo([$expression]))
            ->will($this->returnValue($expression));

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
        $this->assertSame('datetime', $this->type->getName());
    }

    public function testTypes()
    {
        $this->assertSame([
            DateTimeType::TYPE_GREATER_THAN_OR_EQUALS,
            DateTimeType::TYPE_GREATER_THAN,
            DateTimeType::TYPE_LESS_THAN_OR_EQUALS,
            DateTimeType::TYPE_LESS_THAN,
            DateTimeType::TYPE_EQUALS,
            DateTimeType::TYPE_NOT_EQUALS,
            DateTimeType::TYPE_BETWEEN,
            DateTimeType::TYPE_NOT_BETWEEN,
            DateTimeType::TYPE_EMPTY,
            DateTimeType::TYPE_NOT_EMPTY,
        ], DateTimeType::getTypes());
    }

    public function testSimpleTypes()
    {
        $this->assertSame([
            DateTimeType::TYPE_GREATER_THAN_OR_EQUALS,
            DateTimeType::TYPE_GREATER_THAN,
            DateTimeType::TYPE_LESS_THAN_OR_EQUALS,
            DateTimeType::TYPE_LESS_THAN,
            DateTimeType::TYPE_EQUALS,
            DateTimeType::TYPE_NOT_EQUALS,
        ], DateTimeType::getSimpleTypes());
    }

    public function testCompoundTypes()
    {
        $this->assertSame([
            DateTimeType::TYPE_BETWEEN,
            DateTimeType::TYPE_NOT_BETWEEN,
        ], DateTimeType::getCompoundTypes());
    }

    public function testEmptyTypes()
    {
        $this->assertSame([
            DateTimeType::TYPE_EMPTY,
            DateTimeType::TYPE_NOT_EMPTY,
        ], DateTimeType::getEmptyTypes());
    }

    /**
     * @return mixed[]
     */
    public function simpleFilterProvider()
    {
        return [
            'greater_than_or_equals' => [DateTimeType::TYPE_GREATER_THAN_OR_EQUALS, 'gte'],
            'greater_than'           => [DateTimeType::TYPE_GREATER_THAN, 'gt'],
            'less_than_or_equals'    => [DateTimeType::TYPE_LESS_THAN_OR_EQUALS, 'lte'],
            'less_than'              => [DateTimeType::TYPE_LESS_THAN, 'lt'],
            'equals'                 => [DateTimeType::TYPE_EQUALS, 'eq'],
            'not_equals'             => [DateTimeType::TYPE_NOT_EQUALS, 'neq'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function compoundFilterProvider()
    {
        return [
            'between'     => [DateTimeType::TYPE_BETWEEN],
            'not_between' => [DateTimeType::TYPE_NOT_BETWEEN, true],
        ];
    }

    /**
     * @return mixed[]
     */
    public function emptyFilterProvider()
    {
        return [
            'empty'     => [DateTimeType::TYPE_EMPTY, 'isNull'],
            'not_empty' => [DateTimeType::TYPE_NOT_EMPTY, 'isNotNull'],
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
            'missing_value' => [['type' => DateTimeType::TYPE_EQUALS]],
            'invalid_value' => [['type' => DateTimeType::TYPE_EQUALS, 'value' => 'foo']],
            'missing_from'  => [['type' => DateTimeType::TYPE_BETWEEN, 'to' => $datetime = new \DateTime()]],
            'invalid_from'  => [['type' => DateTimeType::TYPE_BETWEEN, 'to' => $datetime, 'from' => 'foo']],
            'missing_to'    => [['type' => DateTimeType::TYPE_BETWEEN, 'from' => $datetime]],
            'invalid_to'    => [['type' => DateTimeType::TYPE_BETWEEN, 'from' => $datetime, 'to' => 'foo']],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->getMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ExpressionBuilderInterface
     */
    private function createExpressionBuilderMock()
    {
        return $this->getMock(ExpressionBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterInterface
     */
    private function createFilterMock()
    {
        return $this->getMock(FilterInterface::class);
    }
}
