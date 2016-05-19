<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber;

use Lug\Bundle\ResourceBundle\Rest\AbstractSubscriber;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceViewSubscriber extends AbstractSubscriber
{
    /**
     * @param ViewEvent $event
     */
    public function onApi(ViewEvent $event)
    {
        if (!$this->getParameterResolver()->resolveApi()) {
            return;
        }

        $groups = $this->getParameterResolver()->resolveSerializerGroups($event->getResource());
        $view = $event->getView();

        if (!empty($groups)) {
            $view->getSerializationContext()->addGroups($groups);
        }

        $view->getSerializationContext()->setSerializeNull($this->getParameterResolver()->resolveSerializerNull());
    }

    /**
     * @param ViewEvent $event
     */
    public function onView(ViewEvent $event)
    {
        if ($this->getParameterResolver()->resolveApi()) {
            return;
        }

        $view = $event->getView();
        $view
            ->setTemplate($this->getParameterResolver()->resolveTemplate())
            ->setData([$view->getTemplateVar() ?: 'data' => $view->getData(), 'resource' => $event->getResource()]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [RestEvents::VIEW => [
            ['onApi', -4000],
            ['onView', -4000],
        ]];
    }
}
