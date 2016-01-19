<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Rest\Action\EventSubscriber;

use FOS\RestBundle\View\View;
use Lug\Bundle\ResourceBundle\Rest\AbstractSubscriber;
use Lug\Bundle\ResourceBundle\Rest\Action\ActionEvent;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ViewActionSubscriber extends AbstractSubscriber
{
    /**
     * @param ActionEvent $event
     */
    public function onAction(ActionEvent $event)
    {
        if ($this->getParameterResolver()->resolveApi()) {
            return;
        }

        $form = $event->getForm();

        if ($form !== null && !$form->isValid()) {
            $view = View::create($form);
        } else {
            $route = $this->getParameterResolver()->resolveRedirectRoute();
            $routeParameters = [];

            if ($form !== null) {
                $routeParameters = $this->getParameterResolver()->resolveRedirectRouteParameters(
                    is_object($data = $form->getData()) ? $data : null,
                    $this->getParameterResolver()->resolveRedirectRouteParametersForward()
                );
            }

            $view = View::createRouteRedirect($route, $routeParameters);
        }

        $event->setView($view);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [RestEvents::ACTION => ['onAction', -1000]];
    }
}
