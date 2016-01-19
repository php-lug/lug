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
class NumberType extends AbstractType
{
    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var int
     */
    private $scale;

    /**
     * @var int
     */
    private $rounding;

    /**
     * @var bool
     */
    private $grouping;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param FormatterInterface        $formatter
     * @param int                       $scale
     * @param int                       $rounding
     * @param bool                      $grouping
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        FormatterInterface $formatter,
        $scale = 2,
        $rounding = \NumberFormatter::ROUND_HALFUP,
        $grouping = false
    ) {
        parent::__construct($propertyAccessor);

        $this->formatter = $formatter;
        $this->scale = $scale;
        $this->grouping = $grouping;
        $this->rounding = $rounding;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        $number = $this->getValue($data, $options);

        if ($number === null) {
            return;
        }

        if (!is_numeric($number)) {
            throw new InvalidTypeException(sprintf(
                'The "%s" %s column type expects a numeric value, got "%s".',
                $options['column']->getName(),
                $this->getName(),
                is_object($number) ? get_class($number) : gettype($number)
            ));
        }

        return $this->formatter->format($number, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'scale'    => $this->scale,
                'rounding' => $this->rounding,
                'grouping' => $this->grouping,
            ])
            ->setAllowedTypes('scale', 'integer')
            ->setAllowedTypes('grouping', 'boolean')
            ->setAllowedValues('rounding', [
                \NumberFormatter::ROUND_FLOOR,
                \NumberFormatter::ROUND_DOWN,
                \NumberFormatter::ROUND_HALFDOWN,
                \NumberFormatter::ROUND_HALFEVEN,
                \NumberFormatter::ROUND_HALFUP,
                \NumberFormatter::ROUND_UP,
                \NumberFormatter::ROUND_CEILING,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'number';
    }
}
