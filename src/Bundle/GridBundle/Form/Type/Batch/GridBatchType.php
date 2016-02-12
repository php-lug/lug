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
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBatchType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $gridBatchSubscriberRegistry;

    /**
     * @param RegistryInterface $gridBatchSubscriberRegistry
     */
    public function __construct(RegistryInterface $gridBatchSubscriberRegistry)
    {
        $this->gridBatchSubscriberRegistry = $gridBatchSubscriberRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $grid = $options['grid'];
        $driver = $grid->getDefinition()->getResource()->getDriver();

        $builder
            ->add('all', GridBatchAllType::class, ['grid' => $grid])
            ->add('type', GridBatchTypeType::class, ['grid' => $grid])
            ->add('batch', SubmitType::class, ['label' => 'lug.batch.submit'])
            ->addEventSubscriber($this->gridBatchSubscriberRegistry[$driver]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('grid')
            ->setAllowedTypes('grid', GridViewInterface::class);
    }
}
