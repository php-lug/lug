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

use Doctrine\Common\Inflector\Inflector;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException;
use Lug\Bundle\ResourceBundle\Rest\AbstractSubscriber;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PagerfantaViewSubscriber extends AbstractSubscriber
{
    /**
     * @var PagerfantaFactory
     */
    private $pagerfantaFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param PagerfantaFactory          $pagerfantaFactory
     * @param RequestStack               $requestStack
     */
    public function __construct(
        ParameterResolverInterface $parameterResolver,
        PagerfantaFactory $pagerfantaFactory,
        RequestStack $requestStack
    ) {
        parent::__construct($parameterResolver);

        $this->pagerfantaFactory = $pagerfantaFactory;
        $this->requestStack = $requestStack;
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

        if (!$data instanceof Pagerfanta) {
            return;
        }

        $data->setCurrentPage($this->getParameterResolver()->resolveCurrentPage());
        $data->setMaxPerPage($this->getParameterResolver()->resolveMaxPerPage());

        if ($this->getParameterResolver()->resolveHateoas()) {
            if (($request = $this->requestStack->getMasterRequest()) === null) {
                throw new RequestNotFoundException();
            }

            $data = $this->pagerfantaFactory->createRepresentation($data, new Route(
                $request->attributes->get('_route'),
                array_merge($request->attributes->get('_route_params', []), $request->query->all())
            ));
        } else {
            $data = array_values(iterator_to_array($data));
        }

        $view->setData($data);
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

        if ($view->getData() instanceof Pagerfanta) {
            $view->setTemplateVar(Inflector::pluralize($event->getResource()->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [RestEvents::VIEW => [
            ['onApi', -3000],
            ['onView', -3000],
        ]];
    }
}
