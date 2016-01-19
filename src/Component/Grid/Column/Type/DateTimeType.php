<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column\Type;

use Lug\Component\Grid\Column\Type\Formatter\FormatterInterface;
use Lug\Component\Grid\Exception\InvalidTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeType extends AbstractType
{
    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var int
     */
    private $dateFormat;

    /**
     * @var int
     */
    private $timeFormat;

    /**
     * @var \IntlTimeZone|\DateTimeZone|string|null
     */
    private $timezone;

    /**
     * @var \IntlCalendar|int|null
     */
    private $calendar;

    /**
     * @var string|null
     */
    private $pattern;

    /**
     * @var bool
     */
    private $lenient;

    /**
     * @param PropertyAccessorInterface               $propertyAccessor
     * @param FormatterInterface                      $formatter
     * @param int                                     $dateFormat
     * @param int                                     $timeFormat
     * @param \IntlTimeZone|\DateTimeZone|string|null $timezone
     * @param \IntlCalendar|int|null                  $calendar
     * @param string|null                             $pattern
     * @param bool                                    $lenient
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        FormatterInterface $formatter,
        $dateFormat = \IntlDateFormatter::MEDIUM,
        $timeFormat = \IntlDateFormatter::MEDIUM,
        $timezone = null,
        $calendar = null,
        $pattern = null,
        $lenient = false
    ) {
        parent::__construct($propertyAccessor);

        $this->formatter = $formatter;
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
        $this->timezone = $timezone;
        $this->calendar = $calendar;
        $this->pattern = $pattern;
        $this->lenient = $lenient;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        $dateTime = $this->getValue($data, $options);

        if ($dateTime === null) {
            return;
        }

        if (!$dateTime instanceof \DateTimeInterface) {
            throw new InvalidTypeException(sprintf(
                'The "%s" %s column type expects a "\DateTimeInterface" value, got "%s".',
                $options['column']->getName(),
                $this->getName(),
                is_object($dateTime) ? get_class($dateTime) : gettype($dateTime)
            ));
        }

        return $this->formatter->format($dateTime, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'calendar'    => $this->calendar,
                'date_format' => $this->dateFormat,
                'lenient'     => $this->lenient,
                'pattern'     => $this->pattern,
                'timezone'    => $this->timezone,
                'time_format' => $this->timeFormat,
            ])
            ->setAllowedTypes('calendar', ['IntlCalendar', 'integer', 'null'])
            ->setAllowedTypes('lenient', 'boolean')
            ->setAllowedTypes('pattern', ['string', 'null'])
            ->setAllowedTypes('timezone', ['IntlTimeZone', 'DateTimeZone', 'string', 'null'])
            ->setAllowedValues('date_format', $formats = [
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::FULL,
            ])
            ->setAllowedValues('time_format', $formats);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'datetime';
    }
}
