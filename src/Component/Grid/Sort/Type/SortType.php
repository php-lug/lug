<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Sort\Type;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Exception\BadMethodCallException;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Model\SortInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SortType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function sort($data, array $options)
    {
        throw new BadMethodCallException(sprintf(
            'The "%s" %s type is a virtual type, you can\'t use it directly.',
            $options['sort']->getName(),
            $this->getName()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['builder', 'grid', 'sort'])
            ->setAllowedTypes('builder', DataSourceBuilderInterface::class)
            ->setAllowedTypes('grid', GridInterface::class)
            ->setAllowedTypes('sort', SortInterface::class);
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
        return 'sort';
    }
}
