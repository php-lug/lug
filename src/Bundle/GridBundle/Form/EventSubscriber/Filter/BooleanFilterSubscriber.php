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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanFilterSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (in_array($data, ['true', true, '1', 1, 'yes', 'on'], true)) {
            $data = 'true';
        } elseif (in_array($data, ['false', false, '0', 0, 'no', 'off'], true)) {
            $data = 'false';
        }

        $event->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SUBMIT => 'onPreSubmit'];
    }
}
