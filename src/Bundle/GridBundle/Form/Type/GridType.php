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

use Lug\Bundle\GridBundle\Form\EventSubscriber\GridSubscriber;
use Lug\Bundle\GridBundle\Form\EventSubscriber\PersistentGridSubscriber;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridType extends AbstractType
{
    /**
     * @var GridSubscriber
     */
    private $gridSubscriber;

    /**
     * @var PersistentGridSubscriber
     */
    private $persistentGridSubscriber;

    /**
     * @param GridSubscriber           $gridSubscriber
     * @param PersistentGridSubscriber $persistentGridSubscriber
     */
    public function __construct(GridSubscriber $gridSubscriber, PersistentGridSubscriber $persistentGridSubscriber)
    {
        $this->gridSubscriber = $gridSubscriber;
        $this->persistentGridSubscriber = $persistentGridSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filters', GridFiltersType::class, ['grid' => $options['grid']])
            ->add('sorting', GridSortingType::class, ['grid' => $options['grid']])
            ->add('page', GridPageType::class)
            ->add('limit', GridLimitType::class, ['grid' => $options['grid']])
            ->add('submit', SubmitType::class, ['label' => 'lug.filter.submit'])
            ->add('reset', SubmitType::class, ['label' => 'lug.filter.reset'])
            ->addEventSubscriber($this->gridSubscriber);

        if ($options['persistent']) {
            $builder->addEventSubscriber(clone $this->persistentGridSubscriber);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'persistent' => function (Options $options) {
                    return $options['grid']->hasOption('persistent')
                        && $options['grid']->getOption('persistent');
                },
                'xml_http_request' => function (Options $options) {
                    return $options['grid']->hasOption('xml_http_request')
                        && $options['grid']->getOption('xml_http_request');
                },
                'method'            => 'GET',
                'validation_groups' => false,
                'csrf_protection'   => false,
            ])
            ->setRequired('grid')
            ->setAllowedTypes('grid', GridInterface::class)
            ->setAllowedTypes('persistent', 'boolean');
    }
}
