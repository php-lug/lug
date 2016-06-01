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

use Lug\Bundle\GridBundle\Form\Type\Batch\GridBatchType;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Bundle\ResourceBundle\Form\FormFactoryInterface;
use Lug\Bundle\ResourceBundle\Rest\AbstractSubscriber;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRendererInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridViewSubscriber extends AbstractSubscriber
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormRendererInterface
     */
    private $formRenderer;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param FormFactoryInterface       $formFactory
     * @param FormRendererInterface      $formRenderer
     */
    public function __construct(
        ParameterResolverInterface $parameterResolver,
        FormFactoryInterface $formFactory,
        FormRendererInterface $formRenderer
    ) {
        parent::__construct($parameterResolver);

        $this->formFactory = $formFactory;
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

        if (is_array($data) && isset($data['grid']) && $data['grid'] instanceof GridViewInterface) {
            $data = $data['grid'];
        }

        if ($data instanceof GridViewInterface) {
            $view->setData($data->getDataSource());
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
        $data = $grid = $view->getData();

        if (is_array($data) && isset($data['grid']) && $data['grid'] instanceof GridViewInterface) {
            $grid = $data['grid'];
        }

        if (!$grid instanceof GridViewInterface) {
            return;
        }

        if ($grid->getBatchForm() === null) {
            $batchForm = !isset($data['batch_form']) || !$data['batch_form'] instanceof FormInterface
                ? $this->formFactory->create(GridBatchType::class, null, ['grid' => $grid])
                : $data['batch_form'];

            $grid->setBatchForm($batchForm->createView());
        }

        $themes = $this->getParameterResolver()->resolveThemes();

        if (!empty($themes)) {
            $this->formRenderer->setTheme($grid->getForm(), $themes);
            $this->formRenderer->setTheme($grid->getBatchForm(), $themes);
        }

        $view
            ->setTemplateVar('grid')
            ->setData($grid);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [RestEvents::VIEW => [
            ['onApi', -2000],
            ['onView', -1000],
        ]];
    }
}
