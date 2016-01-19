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

use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridLimitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label'      => 'lug.limit',
                'empty_data' => function (Options $options) {
                    $grid = $options['grid'];

                    return $grid->hasOption('limit_default') ? (string) $grid->getOption('limit_default') : '10';
                },
                'constraints' => function (Options $options) {
                    $grid = $options['grid'];

                    return new Range([
                        'min'        => $grid->hasOption('limit_min') ? $grid->getOption('limit_min') : 1,
                        'max'        => $grid->hasOption('limit_max') ? $grid->getOption('limit_max') : 100,
                        'minMessage' => 'lug.grid.limit.min',
                        'maxMessage' => 'lug.grid.limit.max',
                    ]);
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
        return IntegerType::class;
    }
}
