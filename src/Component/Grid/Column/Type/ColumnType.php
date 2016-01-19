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

use Lug\Component\Grid\Exception\BadMethodCallException;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        throw new BadMethodCallException(sprintf(
            'The "%s" %s type is a virtual type, you can\'t use it directly.',
            $options['column']->getName(),
            $this->getName()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['column', 'grid'])
            ->setAllowedTypes('column', ColumnInterface::class)
            ->setAllowedTypes('grid', GridViewInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'column';
    }
}
