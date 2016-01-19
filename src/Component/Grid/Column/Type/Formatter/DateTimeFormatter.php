<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column\Type\Formatter;

use Lug\Component\Grid\Context\LocaleContextInterface;
use Lug\Component\Grid\Exception\InvalidTypeException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeFormatter implements FormatterInterface
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function format($value, array $options = [])
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new InvalidTypeException(sprintf(
                'The number formatter expects a numeric value, got "%s".',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $formatter = new \IntlDateFormatter(
            $this->localeContext->getLocale(),
            isset($options['date_format']) ? $options['date_format'] : \IntlDateFormatter::MEDIUM,
            isset($options['time_format']) ? $options['time_format'] : \IntlDateFormatter::MEDIUM,
            isset($options['timezone']) ? $options['timezone'] : $value->getTimezone(),
            isset($options['calendar']) ? $options['calendar'] : null,
            isset($options['pattern']) ? $options['pattern'] : null
        );

        $formatter->setLenient(isset($options['lenient']) ? $options['lenient'] : false);

        return $formatter->format($value);
    }
}
