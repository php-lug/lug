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

use Lug\Bundle\GridBundle\Form\DataTransformer\Filter\BooleanFilterTransformer;
use Lug\Bundle\GridBundle\Form\EventSubscriber\Filter\BooleanFilterSubscriber;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanFilterType extends AbstractFilterType
{
    /**
     * @var BooleanFilterTransformer
     */
    private $booleanFilterTransformer;

    /**
     * @var BooleanFilterSubscriber
     */
    private $booleanFilterSubscriber;

    /**
     * @param BooleanFilterTransformer $booleanFilterTransformer
     * @param BooleanFilterSubscriber  $booleanFilterSubscriber
     */
    public function __construct(
        BooleanFilterTransformer $booleanFilterTransformer,
        BooleanFilterSubscriber $booleanFilterSubscriber
    ) {
        $this->booleanFilterTransformer = $booleanFilterTransformer;
        $this->booleanFilterSubscriber = $booleanFilterSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer($this->booleanFilterTransformer)
            ->addEventSubscriber($this->booleanFilterSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => function (Options $options) {
                return array_combine(
                    array_map(function ($choice) use ($options) {
                        return $options['label_prefix'].'.'.$choice;
                    }, $choices = ['true', 'false']),
                    $choices
                );
            },
            'choices_as_values' => true,
            'placeholder'       => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
