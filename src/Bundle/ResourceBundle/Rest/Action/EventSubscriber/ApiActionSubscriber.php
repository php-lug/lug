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
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ApiActionSubscriber extends AbstractSubscriber
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param UrlGeneratorInterface      $urlGenerator
     */
    public function __construct(ParameterResolverInterface $parameterResolver, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($parameterResolver);

        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param ActionEvent $event
     */
    public function onAction(ActionEvent $event)
    {
        if (!$this->getParameterResolver()->resolveApi()) {
            return;
        }

        $form = $event->getForm();

        if ($form === null) {
            $view = View::create();
        } elseif (!$form->isValid()) {
            $view = View::create($form, Response::HTTP_BAD_REQUEST);
        } else {
            $headers = [];
            $statusCode = $event->getStatusCode();
            $data = $statusCode === Response::HTTP_NO_CONTENT ? null : $form->getData();

            if ($statusCode === Response::HTTP_CREATED) {
                $headers['Location'] = $this->urlGenerator->generate(
                    $this->getParameterResolver()->resolveLocationRoute(),
                    $this->getParameterResolver()->resolveLocationRouteParameters($form->getData())
                );
            }

            $view = View::create($data, $statusCode, $headers);
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
