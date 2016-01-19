<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\Type;

use Lug\Bundle\GridBundle\Form\DataTransformer\GridSortingTransformer;
use Lug\Bundle\GridBundle\Form\Validator\GridSortingConstraint;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridSortingType extends AbstractType
{
    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var GridSortingTransformer
     */
    private $gridSortingTransformer;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param GridSortingTransformer     $gridSortingTransformer
     */
    public function __construct(
        ParameterResolverInterface $parameterResolver,
        GridSortingTransformer $gridSortingTransformer
    ) {
        $this->parameterResolver = $parameterResolver;
        $this->gridSortingTransformer = $gridSortingTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->gridSortingTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'constraints' => function (Options $options) {
                    return new GridSortingConstraint(['grid' => $options['grid']]);
                },
                'error_bubbling' => function (Options $options) {
                    return !$this->parameterResolver->resolveApi();
                },
            ])
            ->setRequired('grid')
            ->setAllowedTypes('grid', GridInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
