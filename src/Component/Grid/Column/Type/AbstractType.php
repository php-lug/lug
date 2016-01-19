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

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractType implements TypeInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('path', function (Options $options, $path) {
                return $path ?: $options['column']->getName();
            })
            ->setAllowedTypes('path', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'column';
    }

    /**
     * @param mixed   $data
     * @param mixed[] $options
     *
     * @return mixed
     */
    protected function getValue($data, array $options)
    {
        return $this->propertyAccessor->getValue($data, $options['path']);
    }
}
