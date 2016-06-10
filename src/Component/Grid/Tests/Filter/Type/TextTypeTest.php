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
use Lug\Component\Grid\Filter\Type\TextType;
use Lug\Component\Grid\Model\FilterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TextTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TextType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new TextType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    /**
     * @dataProvider simpleFilterProvider
     */
    public function testSimpleFilter($type, $method, $dataPrefix = null, $dataSuffix = null)
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
                $this->identicalTo($dataPrefix.($data = 'foo').$dataSuffix)
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
        $this->assertSame('text', $this->type->getName());
    }

    public function testTypes()
    {
        $this->assertSame([
            TextType::TYPE_CONTAINS,
            TextType::TYPE_NOT_CONTAINS,
            TextType::TYPE_EQUALS,
            TextType::TYPE_NOT_EQUALS,
            TextType::TYPE_STARTS_WITH,
            TextType::TYPE_NOT_STARTS_WITH,
            TextType::TYPE_ENDS_WITH,
            TextType::TYPE_NOT_ENDS_WITH,
            TextType::TYPE_EMPTY,
            TextType::TYPE_NOT_EMPTY,
        ], TextType::getTypes());
    }

    public function testSimpleTypes()
    {
        $this->assertSame([
            TextType::TYPE_CONTAINS,
            TextType::TYPE_NOT_CONTAINS,
            TextType::TYPE_EQUALS,
            TextType::TYPE_NOT_EQUALS,
            TextType::TYPE_STARTS_WITH,
            TextType::TYPE_NOT_STARTS_WITH,
            TextType::TYPE_ENDS_WITH,
            TextType::TYPE_NOT_ENDS_WITH,
        ], TextType::getSimpleTypes());
    }

    public function testEmptyTypes()
    {
        $this->assertSame([
            TextType::TYPE_EMPTY,
            TextType::TYPE_NOT_EMPTY,
        ], TextType::getEmptyTypes());
    }

    /**
     * @return mixed[]
     */
    public function simpleFilterProvider()
    {
        return [
            'contains'        => [TextType::TYPE_CONTAINS, 'like', '%', '%'],
            'not_contains'    => [TextType::TYPE_NOT_CONTAINS, 'notLike', '%', '%'],
            'starts_with'     => [TextType::TYPE_STARTS_WITH, 'like', null, '%'],
            'not_starts_with' => [TextType::TYPE_NOT_STARTS_WITH, 'notLike', null, '%'],
            'ends_with'       => [TextType::TYPE_ENDS_WITH, 'like', '%'],
            'not_ends_with'   => [TextType::TYPE_NOT_ENDS_WITH, 'notLike', '%'],
            'equals'          => [TextType::TYPE_EQUALS, 'eq'],
            'not_equals'      => [TextType::TYPE_NOT_EQUALS, 'neq'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function emptyFilterProvider()
    {
        return [
            'empty'     => [TextType::TYPE_EMPTY, 'isNull'],
            'not_empty' => [TextType::TYPE_NOT_EMPTY, 'isNotNull'],
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
            'missing_value' => [['type' => TextType::TYPE_EQUALS]],
            'invalid_value' => [['type' => TextType::TYPE_EQUALS, 'value' => true]],
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
