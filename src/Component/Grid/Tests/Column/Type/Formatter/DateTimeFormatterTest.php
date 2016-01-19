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

use Lug\Component\Grid\Column\Type\Formatter\DateTimeFormatter;
use Lug\Component\Grid\Column\Type\Formatter\FormatterInterface;
use Lug\Component\Grid\Context\LocaleContextInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeFormatter
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
        $this->formatter = new DateTimeFormatter($this->localeContext);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FormatterInterface::class, $this->formatter);
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat(\DateTimeInterface $dateTime, array $options, $expected)
    {
        $this->localeContext
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertSame($expected, $this->formatter->format($dateTime, $options));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidTypeException
     * @expectedExceptionMessage The number formatter expects a numeric value, got "string".
     */
    public function testFormatWithoutDateTime()
    {
        $this->formatter->format('foo', []);
    }

    /**
     * @return mixed[]
     */
    public function formatProvider()
    {
        $icuUpToDate = version_compare(Intl::getIcuVersion(), '50.0', '>=');
        $icuComma = $icuUpToDate ? ',' : '';
        $icuAt = $icuUpToDate ? ' at' : '';

        $datetime = new \DateTime('2015-01-01 00:00:00', new \DateTimeZone('Europe/Paris'));

        return [
            'default' => [
                $datetime,
                [],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM',
            ],
            'date_format_none' => [
                $datetime,
                ['date_format' => \IntlDateFormatter::NONE],
                '12:00:00 AM',
            ],
            'date_format_shot' => [
                $datetime,
                ['date_format' => \IntlDateFormatter::SHORT],
                '1/1/15'.$icuComma.' 12:00:00 AM',
            ],
            'date_format_medium' => [
                $datetime,
                ['date_format' => \IntlDateFormatter::MEDIUM],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM',
            ],
            'date_format_long' => [
                $datetime,
                ['date_format' => \IntlDateFormatter::LONG],
                'January 1, 2015'.$icuAt.' 12:00:00 AM',
            ],
            'date_format_full' => [
                $datetime,
                ['date_format' => \IntlDateFormatter::FULL],
                'Thursday, January 1, 2015'.$icuAt.' 12:00:00 AM',
            ],
            'time_format_none' => [
                $datetime,
                ['time_format' => \IntlDateFormatter::NONE],
                'Jan 1, 2015',
            ],
            'time_format_shot' => [
                $datetime,
                ['time_format' => \IntlDateFormatter::SHORT],
                'Jan 1, 2015'.$icuComma.' 12:00 AM',
            ],
            'time_format_medium' => [
                $datetime,
                ['time_format' => \IntlDateFormatter::MEDIUM],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM',
            ],
            'time_format_long' => [
                $datetime,
                ['time_format' => \IntlDateFormatter::LONG],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM GMT+'.($icuUpToDate ? '1' : '01:00'),
            ],
            'time_format_full' => [
                $datetime,
                ['time_format' => \IntlDateFormatter::FULL],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM Central European'.($icuUpToDate ? ' Standard' : '').' Time',
            ],
            'timezone' => [
                $datetime,
                ['timezone' => 'UTC'],
                'Dec 31, 2014'.$icuComma.' 11:00:00 PM',
            ],
            'int_timezone' => [
                $datetime,
                ['timezone' => \IntlTimeZone::createTimeZone('UTC')],
                'Dec 31, 2014'.$icuComma.' 11:00:00 PM',
            ],
            'date_timezone' => [
                $datetime,
                ['timezone' => new \DateTimeZone('UTC')],
                'Dec 31, 2014'.$icuComma.' 11:00:00 PM',
            ],
            'gregorian_calendar' => [
                $datetime,
                ['calendar' => \IntlDateFormatter::GREGORIAN],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM',
            ],
            'intl_calendar' => [
                $datetime,
                ['calendar' => \IntlCalendar::createInstance('UTC')],
                'Jan 1, 2015'.$icuComma.' 12:00:00 AM',
            ],
            'pattern' => [
                $datetime,
                ['pattern' => 'YYYY/MM/dd'],
                '2015/01/01',
            ],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->getMock(LocaleContextInterface::class);
    }
}
