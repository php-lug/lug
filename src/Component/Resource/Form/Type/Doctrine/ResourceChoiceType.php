<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Form\Type\Doctrine;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class ResourceChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('resource')
            ->setAllowedTypes('resource', ResourceInterface::class)
            ->setDefaults([
                'class' => function (Options $options) {
                    return $options['resource']->getModel();
                },
                'choice_value' => function (Options $options) {
                    return $options['resource']->getIdPropertyPath();
                },
                'choice_label' => function (Options $options) {
                    return $options['resource']->getLabelPropertyPath();
                },
                'placeholder' => '',
            ]);
    }
}
