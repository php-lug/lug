<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Column\Type\Formatter;

use Lug\Component\Grid\Column\Type\Formatter\FormatterInterface;
use Lug\Component\Grid\Column\Type\Formatter\NumberFormatter;
use Lug\Component\Grid\Context\LocaleContextInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class NumberFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NumberFormatter
     */
    private $formatter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private $localeContext;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->localeContext = $this->createLocaleContextMock();
        $this->formatter = new NumberFormatter($this->localeContext);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FormatterInterface::class, $this->formatter);
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat($number, array $options, $expected)
    {
        $this->localeContext
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertSame($expected, $this->formatter->format($number, $options));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidTypeException
     * @expectedExceptionMessage The number formatter expects a numeric value, got "string".
     */
    public function testFormatWithoutNumber()
    {
        $this->formatter->format('foo', []);
    }

    /**
     * @return mixed[]
     */
    public function formatProvider()
    {
        $number = 123456789.123456789;

        return [
            'default' => [
                $number,
                [],
                '123456789.12',
            ],
            'scale' => [
                $number,
                ['scale' => 3],
                '123456789.123',
            ],
            'rounding_floor' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_FLOOR],
                '123456789.1234',
            ],
            'rounding_down' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_DOWN],
                '123456789.1234',
            ],
            'rounding_half_down' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_HALFDOWN],
                '123456789.1235',
            ],
            'rounding_half_even' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_HALFEVEN],
                '123456789.1235',
            ],
            'rounding_half_up' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_HALFUP],
                '123456789.1235',
            ],
            'rounding_up' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_UP],
                '123456789.1235',
            ],
            'rounding_ceiling' => [
                $number,
                ['scale' => 4, 'rounding' => \NumberFormatter::ROUND_CEILING],
                '123456789.1235',
            ],
            'grouping' => [
                $number,
                ['grouping' => true],
                '123,456,789.12',
            ],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->createMock(LocaleContextInterface::class);
    }
}
