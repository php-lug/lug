<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Form\Type;

use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['resource', 'factory'])
            ->setAllowedTypes('resource', ResourceInterface::class)
            ->setAllowedTypes('factory', FactoryInterface::class)
            ->setDefaults([
                'data_class' => function (Options $options) {
                    return $options['resource']->getModel();
                },
                'label_prefix' => function (Options $options) {
                    return 'lug.'.$options['resource']->getName();
                },
                'validation_groups' => function (Options $options) {
                    return [Constraint::DEFAULT_GROUP, 'lug.'.$options['resource']->getName()];
                },
                'empty_data' => function (FormInterface $form) {
                    return $form->isRequired() || !$form->isEmpty()
                        ? $form->getConfig()->getOption('factory')->create()
                        : null;
                },
            ]);
    }
}
