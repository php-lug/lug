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
use Lug\Component\Grid\Column\Type\Formatter\FormatterInterface;
use Lug\Component\Grid\Column\Type\NumberType;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->formatter = $this->createFormatterMock();

        $this->type = new NumberType($this->propertyAccessor, $this->formatter);
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
            ->will($this->returnValue($number = 123.456));

        $this->formatter
            ->expects($this->once())
            ->method('format')
            ->with(
                $this->identicalTo($number),
                $this->identicalTo($options = [
                    'path'     => $path,
                    'scale'    => 4,
                    'grouping' => true,
                    'rounding' => \NumberFormatter::ROUND_HALFDOWN,
                ])
            )
            ->will($this->returnValue($result = '123,45'));

        $this->assertSame($result, $this->type->render($data, $options));
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

        $this->assertNull($this->type->render($data, ['path' => $path]));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidTypeException
     * @expectedExceptionMessage The "name" number column type expects a numeric value, got "stdClass".
     */
    public function testRenderWithoutScalar()
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

        $this->type->render($data, ['column' => $column, 'path' => $path]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'scale'    => 2,
            'rounding' => \NumberFormatter::ROUND_HALFUP,
            'grouping' => false,
        ], $resolver->resolve(['path' => $path]));
    }

    public function testConfigureOptionsWithScale()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'scale'    => $scale = 4,
            'rounding' => \NumberFormatter::ROUND_HALFUP,
            'grouping' => false,
        ], $resolver->resolve(['path' => $path, 'scale' => $scale]));
    }

    public function testConfigureOptionsWithRounding()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'scale'    => 2,
            'rounding' => $rounding = \NumberFormatter::ROUND_HALFDOWN,
            'grouping' => false,
        ], $resolver->resolve(['path' => $path, 'rounding' => $rounding]));
    }

    public function testConfigureOptionsWithGrouping()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'scale'    => 2,
            'rounding' => \NumberFormatter::ROUND_HALFUP,
            'grouping' => $grouping = true,
        ], $resolver->resolve(['path' => $path, 'grouping' => $grouping]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidScale()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['path' => 'path_value', 'scale' => true]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidRounding()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['path' => 'path_value', 'rounding' => true]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidGrouping()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['path' => 'path_value', 'grouping' => 'foo']);
    }

    public function testName()
    {
        $this->assertSame('number', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->createMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    private function createFormatterMock()
    {
        return $this->createMock(FormatterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }
}
