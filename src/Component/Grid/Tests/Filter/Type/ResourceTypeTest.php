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
use Lug\Component\Grid\Filter\Type\ResourceType;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceType
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $resourceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resourceRegistry = $this->createServiceRegistryMock();
        $this->type = new ResourceType($this->resourceRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $this->assertSame($options = [
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'fields'            => ['field'],
            'path'              => null,
            'filter'            => $this->createFilterMock(),
            'resource'          => $this->createResourceMock(),
        ], $resolver->resolve($options));
    }

    public function testConfigureOptionsWithPath()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->type->configureOptions($resolver);

        $this->assertSame($options = [
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'fields'            => ['field'],
            'path'              => 'foo.bar',
            'filter'            => $this->createFilterMock(),
            'resource'          => $this->createResourceMock(),
        ], $resolver->resolve($options));
    }

    public function testConfigureOptionsWithStringResource()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter');

        $this->resourceRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($resourceName = 'resource_name'))
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $this->type->configureOptions($resolver);

        $options = [
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'fields'            => ['field'],
            'path'              => null,
            'filter'            => $this->createFilterMock(),
            'resource'          => $resourceName,
        ];

        $this->assertSame(array_merge($options, ['resource' => $resource]), $resolver->resolve($options));
    }

    /**
     * @dataProvider simpleFilterProvider
     */
    public function testSimpleFilter($type, $method)
    {
        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue(\stdClass::class));

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
                $this->identicalTo($data = new \stdClass())
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
            'resource'          => $resource,
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
    public function testEmptyFilter($type, $method, $model = null)
    {
        $resource = $this->createResourceMock();

        if ($model !== null) {
            $resource
                ->expects($this->once())
                ->method('getModel')
                ->will($this->returnValue($model));
        }

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
            'resource'          => $resource,
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
            'resource'          => $this->createResourceMock(),
            'builder'           => $builder,
            'builder_condition' => AbstractType::CONDITION_AND,
            'fields_condition'  => AbstractType::CONDITION_OR,
            'filter'            => $this->createFilterMock(),
            'fields'            => ['field'],
        ]);
    }

    public function testName()
    {
        $this->assertSame('resource', $this->type->getName());
    }

    public function testTypes()
    {
        $this->assertSame([
            ResourceType::TYPE_EQUALS,
            ResourceType::TYPE_NOT_EQUALS,
            ResourceType::TYPE_EMPTY,
            ResourceType::TYPE_NOT_EMPTY,
        ], ResourceType::getTypes());
    }

    public function testSimpleTypes()
    {
        $this->assertSame([
            ResourceType::TYPE_EQUALS,
            ResourceType::TYPE_NOT_EQUALS,
        ], ResourceType::getSimpleTypes());
    }

    public function testEmptyTypes()
    {
        $this->assertSame([
            ResourceType::TYPE_EMPTY,
            ResourceType::TYPE_NOT_EMPTY,
        ], ResourceType::getEmptyTypes());
    }

    /**
     * @return mixed[]
     */
    public function simpleFilterProvider()
    {
        return [
            'equals'     => [ResourceType::TYPE_EQUALS, 'eq'],
            'not_equals' => [ResourceType::TYPE_NOT_EQUALS, 'neq'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function emptyFilterProvider()
    {
        return [
            'empty'     => [ResourceType::TYPE_EMPTY, 'isNull'],
            'not_empty' => [ResourceType::TYPE_NOT_EMPTY, 'isNotNull'],
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
            'missing_value' => [['type' => ResourceType::TYPE_EQUALS]],
            'invalid_value' => [['type' => ResourceType::TYPE_EQUALS, 'value' => 'foo']],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->createMock(RegistryInterface::class);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
