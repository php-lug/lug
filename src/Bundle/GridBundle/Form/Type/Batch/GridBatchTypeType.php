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

use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBatchTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    return array_combine(
                        array_map(function (BatchInterface $batch) {
                            return $batch->getLabel();
                        }, $batches = $options['grid']->getDefinition()->getBatches()),
                        array_map(function (BatchInterface $batch) {
                            return $batch->getName();
                        }, $batches)
                    );
                },
                'constraints'       => new NotBlank(['message' => 'lug.batch.type.empty']),
                'choices_as_values' => true,
                'label'             => false,
            ])
            ->setRequired('grid')
            ->setAllowedTypes('grid', GridViewInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
