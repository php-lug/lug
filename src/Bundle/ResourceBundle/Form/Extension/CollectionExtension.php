<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form\Extension;

use Lug\Bundle\ResourceBundle\Form\EventSubscriber\CollectionSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CollectionExtension extends AbstractTypeExtension
{
    /**
     * @var CollectionSubscriber
     */
    private $collectionSubscriber;

    /**
     * @param CollectionSubscriber $collectionSubscriber
     */
    public function __construct(CollectionSubscriber $collectionSubscriber)
    {
        $this->collectionSubscriber = $collectionSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['allow_delete']) {
            $builder->addEventSubscriber(clone $this->collectionSubscriber);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['allow_add']) {
            $view->vars['label_add'] = $options['label_add'];
        }

        if ($options['allow_delete']) {
            $view->vars['label_delete'] = $options['label_delete'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'label_add'    => null,
            'label_delete' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CollectionType::class;
    }
}
