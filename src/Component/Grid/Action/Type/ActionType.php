<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Action\Type;

use Lug\Component\Grid\Exception\BadMethodCallException;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        throw new BadMethodCallException(sprintf(
            'The "%s" %s type is a virtual type, you can\'t use it directly.',
            $options['action']->getName(),
            $this->getName()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['action', 'grid'])
            ->setAllowedTypes('action', ActionInterface::class)
            ->setAllowedTypes('grid', GridViewInterface::class);
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
        return 'action';
    }
}
