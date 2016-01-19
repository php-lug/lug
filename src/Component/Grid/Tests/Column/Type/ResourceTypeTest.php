<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Column\Type;

use Lug\Component\Grid\Column\ColumnRendererInterface;
use Lug\Component\Grid\Column\Type\ResourceType;
use Lug\Component\Grid\Column\Type\TypeInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ColumnRendererInterface
     */
    private $renderer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->resourceRegistry = $this->createServiceRegistryMock();
        $this->renderer = $this->createColumnRendererMock();

        $this->type = new ResourceType($this->propertyAccessor, $this->resourceRegistry, $this->renderer);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    public function testRender()
    {
        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue($resourceData = new \stdClass()));

        $resourcePath = 'resource_path';
        $type = 'resource_type';
        $options = ['foo' => 'bar'];

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($grid = $this->createGridViewMock()),
                $this->callback(function (ColumnInterface $column) use ($resourcePath, $type, $options) {
                    return $column->getName() === $resourcePath
                        && $column->getLabel() === null
                        && $column->getType() === $type
                        && $column->getOptions() === $options;
                }),
                $this->identicalTo($resourceData)
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->type->render($data, [
            'path'          => $path,
            'grid'          => $grid,
            'column'        => $this->createColumnMock(),
            'resource'      => $this->createResourceMock(),
            'resource_path' => $resourcePath,
            'type'          => $type,
            'options'       => $options,
        ]));
    }

    public function testRenderWithNull()
    {
        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue(null));

        $this->renderer
            ->expects($this->never())
            ->method('render');

        $this->assertNull($this->type->render($data, ['path' => $path]));
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->resourceRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($resourceName = 'resource_name'))
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($resourcePath = 'resource_path'));

        $this->assertSame([
            'path'          => $path = 'path_value',
            'options'       => [],
            'resource'      => $resource,
            'resource_path' => $resourcePath,
            'type'          => $type = 'resource_type',
        ], $resolver->resolve([
            'path'     => $path,
            'resource' => $resourceName,
            'type'     => $type,
        ]));
    }

    public function testConfigureOptionsWithResourceInstance()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->resourceRegistry
            ->expects($this->never())
            ->method('offsetGet');

        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($resourcePath = 'resource_path'));

        $this->assertSame([
            'path'          => $path = 'path_value',
            'options'       => [],
            'resource'      => $resource,
            'resource_path' => $resourcePath,
            'type'          => $type = 'resource_type',
        ], $resolver->resolve([
            'path'     => $path,
            'resource' => $resource,
            'type'     => $type,
        ]));
    }

    public function testConfigureOptionsWithOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($resourcePath = 'resource_path'));

        $this->assertSame([
            'path'          => $path = 'path_value',
            'options'       => $options = ['foo' => 'bar'],
            'resource'      => $resource,
            'resource_path' => $resourcePath,
            'type'          => $type = 'resource_type',
        ], $resolver->resolve([
            'path'     => $path,
            'resource' => $resource,
            'type'     => $type,
            'options'  => $options,
        ]));
    }

    public function testConfigureOptionsWithResourcePath()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'          => $path = 'path_value',
            'options'       => [],
            'resource_path' => $resourcePath = 'resource_path',
            'resource'      => $resource = $this->createResourceMock(),
            'type'          => $type = 'resource_type',
        ], $resolver->resolve([
            'path'          => $path,
            'resource'      => $resource,
            'type'          => $type,
            'resource_path' => $resourcePath,
        ]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingResource()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path' => 'path_value',
            'type' => 'resource_type',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidResource()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'     => 'path_value',
            'resource' => true,
            'type'     => 'resource_type',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingType()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'     => 'path_value',
            'resource' => $this->createResourceMock(),
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidType()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'     => 'path_value',
            'resource' => $this->createResourceMock(),
            'type'     => true,
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidResourcePath()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'          => 'path_value',
            'resource'      => $this->createResourceMock(),
            'type'          => 'resource_type',
            'resource_path' => true,
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'     => 'path_value',
            'resource' => $this->createResourceMock(),
            'type'     => 'resource_type',
            'options'  => true,
        ]);
    }

    public function testName()
    {
        $this->assertSame('resource', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnRendererInterface
     */
    private function createColumnRendererMock()
    {
        return $this->getMock(ColumnRendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->getMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }
}
