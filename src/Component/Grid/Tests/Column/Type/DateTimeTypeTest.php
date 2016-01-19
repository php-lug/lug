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
use Lug\Component\Grid\Column\Type\DateTimeType;
use Lug\Component\Grid\Column\Type\Formatter\FormatterInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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

        $this->type = new DateTimeType($this->propertyAccessor, $this->formatter);
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
            ->will($this->returnValue($dateTime = new \DateTime($date = date($format = 'Y-m-d H:i:s'))));

        $this->formatter
            ->expects($this->once())
            ->method('format')
            ->with(
                $this->identicalTo($dateTime),
                $this->identicalTo($options = ['path' => $path])
            )
            ->will($this->returnValue($result = $date));

        $this->assertSame($date, $this->type->render($data, $options));
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
     * @expectedExceptionMessage The "name" datetime column type expects a "\DateTimeInterface" value, got "stdClass".
     */
    public function testRenderWithoutDateTime()
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
            'column' => $column,
            'path'   => $path,
        ]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => null,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path]));
    }

    public function testConfigureOptionsWithCalendar()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => $calendar = \IntlDateFormatter::TRADITIONAL,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => null,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'calendar' => $calendar]));
    }

    public function testConfigureOptionsWithIntlCalendar()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => $calendar = \IntlCalendar::createInstance(),
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => null,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'calendar' => $calendar]));
    }

    public function testConfigureOptionsWithDateFormat()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => $dateFormat = \IntlDateFormatter::LONG,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => null,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'date_format' => $dateFormat]));
    }

    public function testConfigureOptionsWithLenient()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => $lenient = true,
            'pattern'     => null,
            'timezone'    => null,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'lenient' => $lenient]));
    }

    public function testConfigureOptionsWithPattern()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => $pattern = 'MM/dd/yyyy',
            'timezone'    => null,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'pattern' => $pattern]));
    }

    public function testConfigureOptionsWithTimezone()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => $timezone = 'Europe/Paris',
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'timezone' => $timezone]));
    }

    public function testConfigureOptionsWithIntlTimezone()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => $timezone = \IntlTimeZone::createTimeZone('Europe/Paris'),
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'timezone' => $timezone]));
    }

    public function testConfigureOptionsWithDateTimezone()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => $timezone = new \DateTimeZone('Europe/Paris'),
            'time_format' => \IntlDateFormatter::MEDIUM,
        ], $resolver->resolve(['path' => $path, 'timezone' => $timezone]));
    }

    public function testConfigureOptionsWithTimeFormat()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'        => $path = 'path_value',
            'calendar'    => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'lenient'     => false,
            'pattern'     => null,
            'timezone'    => null,
            'time_format' => $timeFormat = \IntlDateFormatter::LONG,
        ], $resolver->resolve(['path' => $path, 'time_format' => $timeFormat]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidCalendar()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'     => 'path_value',
            'calendar' => 'foo',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidDateFormat()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'        => 'path_value',
            'date_format' => true,
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidLenient()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'    => 'path_value',
            'lenient' => 'foo',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidPattern()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'    => 'path_value',
            'pattern' => true,
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidTimezone()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'     => 'path_value',
            'timezone' => true,
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidTimeFormat()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'path'        => 'path_value',
            'time_format' => true,
        ]);
    }

    public function testName()
    {
        $this->assertSame('datetime', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    private function createFormatterMock()
    {
        return $this->getMock(FormatterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }
}
