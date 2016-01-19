<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RoutingSubscriber implements EventSubscriberInterface
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $routeParams = $request->attributes->get('_route_params', []);

        if (empty($routeParams)) {
            return;
        }

        $attributes = [];

        foreach (array_keys($routeParams) as $routeParam) {
            if (strpos($routeParam, '_lug_') === 0) {
                $attributes[$routeParam] = true;
            }
        }

        $request->attributes->set(
            '_route_params',
            array_diff_key($routeParams, $attributes)
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 31]];
    }
}
