<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\EventSubscriber\Filter;

use Lug\Component\Grid\Filter\Type\DateTimeType as DateTimeFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as DateTimeForm;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeFilterSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $innerType;

    /**
     * @param string $innerType
     */
    public function __construct($innerType = DateTimeForm::class)
    {
        $this->innerType = $innerType;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $this->buildForm($event->getForm()->getParent(), $event->getData());
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $this->buildForm($event->getForm()->getParent(), $event->getData());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::SUBMIT       => 'onSubmit',
        ];
    }

    /**
     * @param FormInterface $form
     * @param string|null   $data
     */
    private function buildForm(FormInterface $form, $data)
    {
        $form
            ->remove('value')
            ->remove('from')
            ->remove('to');

        if ($data === null || in_array($data, DateTimeFilter::getSimpleTypes(), true)) {
            $form->add('value', $this->innerType);
        } elseif (in_array($data, DateTimeFilter::getCompoundTypes())) {
            $form
                ->add('from', $this->innerType)
                ->add('to', $this->innerType);
        }
    }
}
