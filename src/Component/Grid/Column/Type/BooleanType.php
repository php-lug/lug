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

use Lug\Component\Grid\Exception\InvalidTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanType extends TwigType
{
    /**
     * @var string
     */
    private $template;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param \Twig_Environment         $twig
     * @param string                    $template
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, \Twig_Environment $twig, $template)
    {
        parent::__construct($propertyAccessor, $twig);

        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        $boolean = $this->getValue($data, $options);

        if ($boolean === null) {
            return;
        }

        if (!is_bool($boolean)) {
            throw new InvalidTypeException(sprintf(
                'The "%s" %s column type expects a boolean value, got "%s".',
                $options['column']->getName(),
                $this->getName(),
                is_object($boolean) ? get_class($boolean) : gettype($boolean)
            ));
        }

        return $this->renderTemplate($boolean, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('template', $this->template);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'boolean';
    }
}
