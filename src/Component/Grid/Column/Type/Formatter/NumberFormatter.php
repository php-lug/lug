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
class NumberFormatter implements FormatterInterface
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
        if (!is_numeric($value)) {
            throw new InvalidTypeException(sprintf(
                'The number formatter expects a numeric value, got "%s".',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $scale = isset($options['scale']) ? $options['scale'] : 2;
        $rounding = isset($options['rounding']) ? $options['rounding'] : \NumberFormatter::ROUND_HALFUP;
        $grouping = isset($options['grouping']) ? $options['grouping'] : false;

        $formatter = new \NumberFormatter($this->localeContext->getLocale(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $scale);
        $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, $rounding);
        $formatter->setAttribute(\NumberFormatter::GROUPING_USED, $grouping);

        return str_replace("\xc2\xa0", ' ', $formatter->format($value));
    }
}
