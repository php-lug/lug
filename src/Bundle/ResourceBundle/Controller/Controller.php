<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Lug\Bundle\GridBundle\Batch\BatcherInterface;
use Lug\Bundle\GridBundle\Form\Type\Batch\GridBatchType;
use Lug\Bundle\GridBundle\Form\Type\GridType;
use Lug\Bundle\GridBundle\Handler\GridHandlerInterface;
use Lug\Bundle\ResourceBundle\Form\FormFactoryInterface;
use Lug\Bundle\ResourceBundle\Form\Type\CsrfProtectionType;
use Lug\Bundle\ResourceBundle\Rest\Action\ActionEvent;
use Lug\Bundle\ResourceBundle\Rest\RestEvents;
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Bundle\ResourceBundle\Security\SecurityCheckerInterface;
use Lug\Component\Grid\Model\Builder\GridBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Resource\Controller\ControllerInterface;
use Lug\Component\Resource\Domain\DomainManagerInterface;
use Lug\Component\Resource\Exception\DomainException;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Controller extends FOSRestController implements ControllerInterface
{
    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @param ResourceInterface $resource
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->processView($this->view($this->find('index', false)));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function gridAction(Request $request)
    {
        return $this->processView($this->view([
            'form' => $form = $this->submitGrid($grid = $this->buildGrid(), $request),
            'grid' => $this->getGridHandler()->handle($grid, $form),
        ]));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function batchAction(Request $request)
    {
        $view = $this->getGridHandler()->handle(
            $grid = $this->buildGrid(),
            $form = $this->submitGrid($grid, $request)
        );

        if (($api = $this->getParameterResolver()->resolveApi()) && !$form->isValid()) {
            return $this->processAction($form);
        }

        $batchForm = $this->buildForm(GridBatchType::class, null, ['grid' => $view]);

        if ($this->submitForm($batchForm, $request)->isValid() && $form->isValid()) {
            try {
                $this->getGridBatcher()->batch($grid, $batchForm);
            } catch (DomainException $e) {
                $this->processException($e);
            }
        }

        if (!$api && !$batchForm->isValid()) {
            return $this->processView($this->view([
                'batch_form' => $batchForm,
                'form'       => $form,
                'grid'       => $view,
            ]));
        }

        return $this->processAction($batchForm);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        return $this->processView($this->view($this->find('show')));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->buildForm();

        if ($request->isMethod('POST') && $this->submitForm($form, $request)->isValid()) {
            try {
                $this->getDomainManager()->create($form->getData());
            } catch (DomainException $e) {
                $this->processException($e);
            }
        }

        return $this->processAction($form, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        $form = $this->buildForm(null, $this->find('update'));

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)
            && $this->submitForm($form, $request)->isValid()) {
            try {
                $this->getDomainManager()->update($form->getData());
            } catch (DomainException $e) {
                $this->processException($e);
            }
        }

        return $this->processAction($form);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        if ($this->submitForm($this->buildForm(CsrfProtectionType::class), $request)->isValid()) {
            try {
                $this->getDomainManager()->delete($this->find('delete'));
            } catch (DomainException $e) {
                $this->processException($e);
            }
        }

        return $this->processAction();
    }

    /**
     * @param string $action
     * @param bool   $mandatory
     *
     * @return object|object[]
     */
    protected function find($action, $mandatory = true)
    {
        $repositoryMethod = $this->getParameterResolver()->resolveRepositoryMethod($action);
        $criteria = $this->getParameterResolver()->resolveCriteria($mandatory);
        $sorting = $this->getParameterResolver()->resolveSorting();

        if (($result = $this->getDomainManager()->find($action, $repositoryMethod, $criteria, $sorting)) === null) {
            throw $this->createNotFoundException(sprintf(
                'The %s does not exist (%s) (%s).',
                str_replace('_', ' ', $this->resource->getName()),
                json_encode($criteria),
                json_encode($sorting)
            ));
        }

        if ($result instanceof Pagerfanta) {
            $result->setCurrentPage($this->getParameterResolver()->resolveCurrentPage());
            $result->setMaxPerPage($this->getParameterResolver()->resolveMaxPerPage());
        } elseif (!$this->getSecurityChecker()->isGranted($action, $result)) {
            throw $this->createAccessDeniedException();
        }

        return $result;
    }

    /**
     * @param string|object|null $form
     * @param object|null        $object
     * @param mixed[]            $options
     *
     * @return FormInterface
     */
    protected function buildForm($form = null, $object = null, array $options = [])
    {
        return $this->getFormFactory()->create($form ?: $this->resource, $object, $options);
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return FormInterface
     */
    protected function submitForm(FormInterface $form, Request $request)
    {
        $bag = $request->isMethod('GET') || $form->getConfig()->getMethod() === 'GET'
            ? $request->query
            : $request->request;

        if ($this->getParameterResolver()->resolveApi()) {
            $data = $bag->all();
        } else {
            $data = $bag->get($form->getName(), []);
        }

        array_walk_recursive($data, function (&$value) {
            if ($value === false) {
                $value = 'false';
            }
        });

        return $form->submit($data, !$request->isMethod('PATCH'));
    }

    /**
     * @return GridInterface
     */
    protected function buildGrid()
    {
        return $this->getGridBuilder()->build($this->getParameterResolver()->resolveGrid($this->resource));
    }

    /**
     * @param GridInterface $grid
     * @param Request       $request
     *
     * @return FormInterface
     */
    protected function submitGrid(GridInterface $grid, Request $request)
    {
        return $this->submitForm($this->buildForm(GridType::class, null, ['grid' => $grid]), $request);
    }

    /**
     * @param FormInterface|null $form
     * @param int                $statusCode
     *
     * @return Response
     */
    protected function processAction(FormInterface $form = null, $statusCode = Response::HTTP_NO_CONTENT)
    {
        $this->getRestEventDispatcher()->dispatch(
            RestEvents::ACTION,
            $event = new ActionEvent($this->resource, $form, $statusCode)
        );

        $view = $event->getView();
        $statusCode = $view->getStatusCode();

        return $statusCode >= 300 && $statusCode < 400
            ? $this->handleView($view)
            : $this->processView($view);
    }

    /**
     * @param View $view
     *
     * @return Response
     */
    protected function processView(View $view)
    {
        $this->getRestEventDispatcher()->dispatch(RestEvents::VIEW, $event = new ViewEvent($this->resource, $view));

        return $this->handleView($event->getView());
    }

    /**
     * @param DomainException $domainException
     */
    protected function processException(DomainException $domainException)
    {
        if ($this->getParameterResolver()->resolveApi()) {
            throw new HttpException(
                $domainException->getStatusCode() ?: 500,
                $domainException->getMessage() ?: 'Internal Server Error',
                $domainException
            );
        }
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->get('lug.resource.form.factory');
    }

    /**
     * @return FactoryInterface
     */
    protected function getFactory()
    {
        return $this->get('lug.resource.registry.factory')[$this->resource->getName()];
    }

    /**
     * @return DomainManagerInterface
     */
    protected function getDomainManager()
    {
        return $this->get('lug.resource.registry.domain_manager')[$this->resource->getName()];
    }

    /**
     * @return SecurityCheckerInterface
     */
    protected function getSecurityChecker()
    {
        return $this->get('lug.resource.security.checker');
    }

    /**
     * @return ParameterResolverInterface
     */
    protected function getParameterResolver()
    {
        return $this->get('lug.resource.routing.parameter_resolver');
    }

    /**
     * @return GridBuilderInterface
     */
    protected function getGridBuilder()
    {
        return $this->get('lug.grid.builder');
    }

    /**
     * @return GridHandlerInterface
     */
    protected function getGridHandler()
    {
        return $this->get('lug.grid.handler');
    }

    /**
     * @return BatcherInterface
     */
    protected function getGridBatcher()
    {
        return $this->get('lug.grid.batcher');
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getRestEventDispatcher()
    {
        return $this->get('lug.resource.rest.event_dispatcher');
    }
}
