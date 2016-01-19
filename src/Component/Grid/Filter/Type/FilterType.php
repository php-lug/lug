<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Filter\Type;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Exception\BadMethodCallException;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($data, array $options)
    {
        throw new BadMethodCallException(sprintf(
            'The "%s" %s type is a virtual type, you can\'t use it directly.',
            $options['filter']->getName(),
            $this->getName()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['builder', 'filter', 'grid'])
            ->setAllowedTypes('builder', DataSourceBuilderInterface::class)
            ->setAllowedTypes('filter', FilterInterface::class)
            ->setAllowedTypes('grid', GridInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filter';
    }
}
