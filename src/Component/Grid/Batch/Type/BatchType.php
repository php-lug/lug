<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Batch\Type;

use Lug\Component\Grid\Exception\BadMethodCallException;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function batch($data, array $options)
    {
        throw new BadMethodCallException(sprintf(
            'The "%s" %s type is a virtual type, you can\'t use it directly.',
            $options['batch']->getName(),
            $this->getName()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['batch', 'grid'])
            ->setAllowedTypes('batch', BatchInterface::class)
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
        return 'batch';
    }
}
