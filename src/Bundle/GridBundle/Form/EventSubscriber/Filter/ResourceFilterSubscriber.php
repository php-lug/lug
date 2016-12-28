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

use Lug\Component\Grid\Filter\Type\ResourceType;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceFilterSubscriber implements EventSubscriberInterface
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
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
        $filter = $form->getConfig()->getOption('filter');

        if ($filter->hasOption('form')) {
            $resourceForm = $filter->getOption('form');
        } else {
            $resource = $filter->hasOption('resource') ? $filter->getOption('resource') : $filter->getName();
            $resourceForm = $this->resourceRegistry[$resource]->getChoiceForm();
        }

        $form->remove('value');

        if ($data === null || in_array($data, ResourceType::getSimpleTypes(), true)) {
            $form->add('value', $resourceForm);
        } elseif (in_array($data, ResourceType::getCompoundTypes(), true)) {
            $form->add('value', CollectionType::class, [
                'entry_type'   => $resourceForm,
                'allow_add'    => true,
                'allow_delete' => true,
            ]);
        }
    }
}
