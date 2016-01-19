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
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormViewSubscriber extends AbstractSubscriber
{
    /**
     * @var FormRendererInterface
     */
    private $formRenderer;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param FormRendererInterface      $formRenderer
     */
    public function __construct(ParameterResolverInterface $parameterResolver, FormRendererInterface $formRenderer)
    {
        parent::__construct($parameterResolver);

        $this->formRenderer = $formRenderer;
    }

    /**
     * @param ViewEvent $event
     */
    public function onApi(ViewEvent $event)
    {
        if (!$this->getParameterResolver()->resolveApi()) {
            return;
        }

        $view = $event->getView();
        $data = $view->getData();

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $value) {
            if (!$value instanceof FormInterface || !$value->isSubmitted() || $value->isValid()) {
                continue;
            }

            $view
                ->setData($value)
                ->setStatusCode(Response::HTTP_BAD_REQUEST);

            break;
        }
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
        $data = $view->getData();

        if ($data instanceof FormInterface) {
            $view
                ->setTemplateVar('form')
                ->setData($this->createFormView($data));

            return;
        }

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof FormInterface) {
                $data[$key] = $this->createFormView($value);
            }
        }

        $view->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [RestEvents::VIEW => [
            ['onApi', -1000],
            ['onView', -2000],
        ]];
    }

    /**
     * @param FormInterface $form
     *
     * @return FormView
     */
    private function createFormView(FormInterface $form)
    {
        $themes = $this->getParameterResolver()->resolveThemes();
        $view = $form->createView();

        if (!empty($themes)) {
            $this->formRenderer->setTheme($view, $themes);
        }

        return $view;
    }
}
