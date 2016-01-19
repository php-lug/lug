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

use Lug\Component\Grid\Column\Type\AbstractType;
use Lug\Component\Grid\Column\Type\JsonType;
use Lug\Component\Grid\Exception\InvalidTypeException;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class JsonTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonType
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->type = new JsonType($this->propertyAccessor);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
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
            ->will($this->returnValue($result = ['foo' => 'bar']));

        $this->assertSame(json_encode($result), $this->type->render($data, [
            'path'    => $path,
            'options' => 0,
            'depth'   => 512,
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
            ->will($this->returnValue($result = null));

        $this->assertNull($this->type->render($data, [
            'path'    => $path,
            'options' => 0,
            'depth'   => 512,
        ]));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidTypeException
     * @expectedExceptionMessage The "name" json column type expects anything except a resource.
     */
    public function testRenderWithResource()
    {
        $resource = fopen('php://temp', 'r');

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue($resource));

        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        try {
            $this->type->render($data, [
                'column'  => $column,
                'path'    => $path,
                'options' => 0,
                'depth'   => 512,
            ]);

            $this->fail();
        } catch (InvalidTypeException $e) {
        }

        if (is_resource($resource)) {
            fclose($resource);
        }

        if (isset($e)) {
            throw $e;
        }
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'    => $path = 'path_value',
            'options' => 0,
            'depth'   => 512,
        ], $resolver->resolve(['path' => $path]));
    }

    public function testConfigureOptionsWithExplicitOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'    => $path = 'path_value',
            'options' => $options = JSON_HEX_TAG | JSON_HEX_AMP,
            'depth'   => 512,
        ], $resolver->resolve(['path' => $path, 'options' => $options]));
    }

    public function testConfigureOptionsWithExplicitDepth()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'    => $path = 'path_value',
            'options' => 0,
            'depth'   => $depth = 256,
        ], $resolver->resolve(['path' => $path, 'depth' => $depth]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'    => 'path_value',
            'options' => true,
            'depth'   => 512,
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidDepth()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'    => 'path_value',
            'options' => 0,
            'depth'   => true,
        ]);
    }

    public function testName()
    {
        $this->assertSame('json', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }
}
