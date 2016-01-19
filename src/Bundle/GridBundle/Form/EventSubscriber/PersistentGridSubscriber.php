<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\EventSubscriber;

use Lug\Bundle\GridBundle\Filter\FilterManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * WARNING - This event subscriber is statefull.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PersistentGridSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var mixed[]
     */
    private $submittedData;

    /**
     * @param FilterManagerInterface $filterManager
     */
    public function __construct(FilterManagerInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $grid = $event->getForm()->getConfig()->getOption('grid');

        if (isset($data['reset'])) {
            $this->submittedData = $grid->getData();
        } else {
            $this->submittedData = array_merge(
                $this->filterManager->get($grid),
                $event->getData()
            );
        }

        $event->setData($this->submittedData);
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        if ($form->isValid()) {
            unset($this->submittedData['page']);
            unset($this->submittedData['reset']);
            unset($this->submittedData['_xml_http_request']);

            $this->filterManager->set($form->getConfig()->getOption('grid'), $this->submittedData);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT  => 'onPreSubmit',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }
}
