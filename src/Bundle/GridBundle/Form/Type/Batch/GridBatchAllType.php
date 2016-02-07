<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\Type\Batch;

use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBatchAllType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => function (Options $options) {
                    return 'lug.'.$options['grid']->getDefinition()->getResource()->getName().'.batch.all';
                },
                'label_translation_arguments' => function (Options $options) {
                    return ['%count%' => count($options['grid']->getDataSource())];
                },
            ])
            ->setRequired('grid')
            ->setAllowedTypes('grid', GridViewInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CheckboxType::class;
    }
}
