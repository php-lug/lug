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
use Lug\Component\Grid\Filter\Type\TypeInterface;
use Lug\Component\Grid\Model\FilterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbstractType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = $this->getMockForAbstractClass(AbstractType::class);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    public function testFilter()
    {
        $this->type
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->identicalTo($field = 'field'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($options = [
                    'builder'           => $builder = $this->createDataSourceBuilderMock(),
                    'builder_condition' => AbstractType::CONDITION_AND,
                    'fields_condition'  => AbstractType::CONDITION_OR,
                    'filter'            => $this->createFilterMock(),
                    'fields'            => [$field],
                ])
            )
            ->will($this->returnValue($expression = 'expression'));

        $builder
            ->expects($this->once())
            ->method('getExpressionBuilder')
            ->will($this->returnValue($expressionBuilder = $this->createExpressionBuilderMock()));

        $expressionBuilder
            ->expects($this->once())
            ->method('orX')
            ->with($this->identicalTo([$expression]))
            ->will($this->returnValue($expression));

        $builder
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->identicalTo($expression));

        $this->type->filter('data', $options);
    }

    public function testFilterWithBuilderConditionOr()
    {
        $this->type
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->identicalTo($field = 'field'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($options = [
                    'builder'           => $builder = $this->createDataSourceBuilderMock(),
                    'builder_condition' => AbstractType::CONDITION_OR,
                    'fields_condition'  => AbstractType::CONDITION_OR,
                    'filter'            => $this->createFilterMock(),
                    'fields'            => [$field],
                ])
            )
            ->will($this->returnValue($expression = 'expression'));

        $builder
            ->expects($this->once())
            ->method('getExpressionBuilder')
            ->will($this->returnValue($expressionBuilder = $this->createExpressionBuilderMock()));

        $expressionBuilder
            ->expects($this->once())
            ->method('orX')
            ->with($this->identicalTo([$expression]))
            ->will($this->returnValue($expression));

        $builder
            ->expects($this->once())
            ->method('orWhere')
            ->with($this->identicalTo($expression));

        $this->type->filter('data', $options);
    }

    public function testFilterWithFieldsConditionAnd()
    {
        $this->type
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->identicalTo($field = 'field'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($options = [
                    'builder'           => $builder = $this->createDataSourceBuilderMock(),
                    'builder_condition' => AbstractType::CONDITION_AND,
                    'fields_condition'  => AbstractType::CONDITION_AND,
                    'filter'            => $this->createFilterMock(),
                    'fields'            => [$field],
                ])
            )
            ->will($this->returnValue($expression = 'expression'));

        $builder
            ->expects($this->once())
            ->method('getExpressionBuilder')
            ->will($this->returnValue($expressionBuilder = $this->createExpressionBuilderMock()));

        $expressionBuilder
            ->expects($this->once())
            ->method('andX')
            ->with($this->identicalTo([$expression]))
            ->will($this->returnValue($expression));

        $builder
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->identicalTo($expression));

        $this->type->filter('data', $options);
    }

    public function testFilterWithNull()
    {
        $this->type->filter(null, [
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $this->createFilterMock(),
            'fields'            => ['field'],
        ]);
    }

    public function testFilterWithoutExpressions()
    {
        $this->type
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->identicalTo($field = 'field'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($options = [
                    'builder'           => $builder = $this->createDataSourceBuilderMock(),
                    'builder_condition' => AbstractType::CONDITION_AND,
                    'fields_condition'  => AbstractType::CONDITION_OR,
                    'filter'            => $this->createFilterMock(),
                    'fields'            => [$field],
                ])
            )
            ->will($this->returnValue(null));

        $this->type->filter('data', $options);
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $filter = $this->createFilterMock();
        $filter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($filterName = 'filter_name'));

        $this->assertSame([
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $filter,
            'fields'            => [$filterName],
        ], $resolver->resolve(['filter' => $filter]));
    }

    public function testConfigureOptionsWithBuilderCondition()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $filter = $this->createFilterMock();
        $filter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($filterName = 'filter_name'));

        $this->assertSame([
            'builder_condition' => $builderCondition = AbstractType::CONDITION_OR,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $filter,
            'fields'            => [$filterName],
        ], $resolver->resolve(['builder_condition' => $builderCondition, 'filter' => $filter]));
    }

    public function testConfigureOptionsWithFieldsCondition()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $filter = $this->createFilterMock();
        $filter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($filterName = 'filter_name'));

        $this->assertSame([
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => $fieldsCondition = AbstractType::CONDITION_AND,
            'filter'            => $filter,
            'fields'            => [$filterName],
        ], $resolver->resolve(['fields_condition' => $fieldsCondition, 'filter' => $filter]));
    }

    public function testConfigureOptionsWithFields()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $this->assertSame([
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'fields'            => $fields = ['field'],
            'filter'            => $filter = $this->createFilterMock(),
        ], $resolver->resolve(['fields' => $fields, 'filter' => $filter]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureWithInvalidBuilderCondition()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $resolver->resolve(['filter' => $this->createFilterMock(), 'builder_condition' => 'foo']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureWithInvalidFieldsCondition()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $resolver->resolve(['filter' => $this->createFilterMock(), 'fields_condition' => 'foo']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureWithInvalidFields()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $resolver->resolve(['filter' => $this->createFilterMock(), 'fields' => 'foo']);
    }

    public function testParent()
    {
        $this->assertSame('filter', $this->type->getParent());
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
