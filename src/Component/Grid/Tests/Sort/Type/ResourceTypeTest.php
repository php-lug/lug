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
use Lug\Component\Grid\Sort\Type\ResourceType;
use Lug\Component\Grid\Sort\Type\TypeInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
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
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $repositoryRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resourceRegistry = $this->createServiceRegistryMock();
        $this->repositoryRegistry = $this->createServiceRegistryMock();

        $this->type = new ResourceType($this->resourceRegistry, $this->repositoryRegistry);
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
            ->method('getAliases')
            ->will($this->returnValue([]));

        $builder
            ->expects($this->once())
            ->method('getProperty')
            ->with($this->identicalTo($resourceField = 'resource_field'))
            ->will($this->returnValue($resourceProperty = 'resource_property'));

        $builder
            ->expects($this->once())
            ->method('leftJoin')
            ->with(
                $this->identicalTo($resourceProperty),
                $this->identicalTo($resourceField)
            );

        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($resourcePath = 'resource_path'));

        $this->repositoryRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($repository = $this->createRepositoryMock()));

        $repository
            ->expects($this->once())
            ->method('getProperty')
            ->with(
                $this->identicalTo($resourceField),
                $this->identicalTo($resourcePath)
            )
            ->will($this->returnValue($property = 'property'));

        $builder
            ->expects($this->once())
            ->method('orderBy')
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($data = 'data')
            );

        $this->type->sort($data, [
            'resource' => $resource,
            'builder'  => $builder,
            'field'    => $resourceField,
        ]);
    }

    public function testSortWithJoin()
    {
        $builder = $this->createDataSourceBuilderMock();
        $builder
            ->expects($this->once())
            ->method('getAliases')
            ->will($this->returnValue([$resourceField = 'resource_field']));

        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($resourcePath = 'resource_path'));

        $this->repositoryRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($repository = $this->createRepositoryMock()));

        $repository
            ->expects($this->once())
            ->method('getProperty')
            ->with(
                $this->identicalTo($resourceField),
                $this->identicalTo($resourcePath)
            )
            ->will($this->returnValue($property = 'property'));

        $builder
            ->expects($this->once())
            ->method('orderBy')
            ->with(
                $this->identicalTo($property),
                $this->identicalTo($data = 'data')
            );

        $this->type->sort($data, [
            'resource' => $resource,
            'builder'  => $builder,
            'field'    => $resourceField,
        ]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'field'    => 'field',
            'resource' => $this->createResourceMock(),
        ], $resolver->resolve($options));
    }

    public function testConfigureOptionsWithStringResource()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->resourceRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name = 'name'))
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $this->assertSame([
            'field'    => $field = 'field',
            'resource' => $resource,
        ], $resolver->resolve([
            'field'    => $field,
            'resource' => $name,
        ]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidResource()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['field' => 'field', 'resource' => true]);
    }

    public function testName()
    {
        $this->assertSame('resource', $this->type->getName());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->createMock(RepositoryInterface::class);
    }
}
