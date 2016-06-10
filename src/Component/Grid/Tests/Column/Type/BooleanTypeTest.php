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

use Lug\Component\Grid\Column\Type\BooleanType;
use Lug\Component\Grid\Column\Type\TwigType;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $template;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->twig = $this->createTwigEnvironmentMock();
        $this->template = 'template_name';

        $this->type = new BooleanType($this->propertyAccessor, $this->twig, $this->template);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TwigType::class, $this->type);
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
            ->will($this->returnValue($boolean = true));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($this->template),
                $this->identicalTo(array_merge(
                    $context = ['foo' => 'bar'],
                    ['column' => $column = $this->createColumnMock(), 'data' => $boolean]
                ))
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->type->render($data, [
            'column'   => $column,
            'path'     => $path,
            'template' => $this->template,
            'context'  => $context,
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

        $this->assertNull($this->type->render($data, [
            'path'     => $path,
            'template' => $this->template,
            'context'  => [],
        ]));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidTypeException
     * @expectedExceptionMessage The "name" boolean column type expects a boolean value, got "stdClass".
     */
    public function testRenderWithoutBoolean()
    {
        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue(new \stdClass()));

        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->type->render($data, [
            'column'   => $column,
            'path'     => $path,
            'template' => $this->template,
            'context'  => [],
        ]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'context'  => [],
            'template' => $this->template,
        ], $resolver->resolve(array_merge(['path' => $path])));
    }

    public function testConfigureOptionsWithTemplate()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'context'  => [],
            'template' => $template = 'template_custom',
        ], $resolver->resolve(array_merge(['path' => $path, 'template' => $template])));
    }

    public function testConfigureOptionsWithContext()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'path'     => 'path_value',
            'context'  => ['context'],
            'template' => 'template_name',
        ], $resolver->resolve($options));
    }

    public function testName()
    {
        $this->assertSame('boolean', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->createMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    private function createTwigEnvironmentMock()
    {
        return $this->createMock(\Twig_Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }
}
