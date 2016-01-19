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

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBatchValueType extends AbstractType
{
    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param PropertyAccessorInterface  $propertyAccessor
     */
    public function __construct(
        ParameterResolverInterface $parameterResolver,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->parameterResolver = $parameterResolver;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $idPropertyPath = function (Options $options) {
            $propertyPath = $options['grid']->getDefinition()->getResource()->getIdPropertyPath();

            return function ($choice) use ($propertyPath) {
                return $this->propertyAccessor->getValue($choice, $propertyPath);
            };
        };

        $labelPropertyPath = function (Options $options) {
            $propertyPath = $options['grid']->getDefinition()->getResource()->getLabelPropertyPath();

            return function ($choice) use ($propertyPath) {
                return $this->propertyAccessor->getValue($choice, $propertyPath);
            };
        };

        $resolver
            ->setDefaults([
                'multiple'           => true,
                'translation_domain' => false,
                'choices'            => [],
                'choices_as_values'  => true,
                'choice_name'        => $idPropertyPath,
                'choice_value'       => $idPropertyPath,
                'choice_label'       => $labelPropertyPath,
                'class'              => function (Options $options) {
                    return $options['grid']->getDefinition()->getResource()->getModel();
                },
                'expanded' => function (Options $options) {
                    return !$this->parameterResolver->resolveApi();
                },
                'constraints' => function (Options $options) {
                    $resource = $options['grid']->getDefinition()->getResource();

                    return [new Count([
                        'min'        => 1,
                        'minMessage' => 'lug.'.$resource->getName().'.batch.empty',
                    ])];
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
        return ChoiceType::class;
    }
}
