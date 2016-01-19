<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\Type\Filter;

use Lug\Component\Grid\Model\FilterInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => function (Options $options) {
                    return $options['filter']->getLabel();
                },
                'label_prefix' => function (Options $options) {
                    return 'lug.filter.'.$options['filter']->getType();
                },
            ])
            ->setRequired('filter')
            ->setAllowedTypes('filter', FilterInterface::class);
    }
}
