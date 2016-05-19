<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Routing;

use Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException;
use Lug\Bundle\ResourceBundle\Exception\RuntimeException;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ParameterResolver implements ParameterResolverInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param RequestStack              $requestStack
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(RequestStack $requestStack, PropertyAccessorInterface $propertyAccessor)
    {
        $this->requestStack = $requestStack;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveApi()
    {
        $default = false;

        if (($request = $this->resolveRequest()) === null) {
            return $default;
        }

        return $this->resolveParameter('api', $default) || $request->getRequestFormat() !== 'html';
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCriteria($mandatory = false)
    {
        if (($request = $this->resolveRequest()) === null) {
            if ($mandatory) {
                throw new RequestNotFoundException();
            }

            return [];
        }

        $value = $this->resolveParameter('criteria', ['id']);

        if (empty($value) && $mandatory) {
            throw new RuntimeException(sprintf(
                'The criteria could not be found for the route "%s".',
                $request->attributes->get('_route')
            ));
        }

        $criteria = [];

        foreach ($value as $identifier) {
            $value = $request->get($identifier);

            if ($value === null) {
                throw new RuntimeException(sprintf(
                    'The criteria "%s" could not be found for the route "%s".',
                    $identifier,
                    $request->attributes->get('_route')
                ));
            }

            $criteria[$identifier] = $value;
        }

        return $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCurrentPage()
    {
        $default = 1;

        if (($request = $this->resolveRequest()) === null) {
            return $default;
        }

        return $request->get($this->resolveParameter('page_parameter', 'page'), $default);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveForm(ResourceInterface $resource)
    {
        return $this->resolveParameter('form', $resource->getForm());
    }

    /**
     * {@inheritdoc}
     */
    public function resolveGrid(ResourceInterface $resource)
    {
        if (($request = $this->resolveRequest()) === null) {
            throw new RequestNotFoundException();
        }

        return array_merge(['resource' => $resource], $this->resolveParameter('grid', []));
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHateoas()
    {
        return $this->resolveParameter('hateoas', false);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveLocationRoute()
    {
        if (($request = $this->resolveRequest()) === null) {
            throw new RequestNotFoundException();
        }

        $locationRoute = $this->resolveParameter('location_route');

        if (empty($locationRoute)) {
            throw new RuntimeException(sprintf(
                'The location route could not be found for the route "%s".',
                $request->attributes->get('_route')
            ));
        }

        return $locationRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveLocationRouteParameters($object)
    {
        return $this->resolveRouteParameter('location_route_parameters', $object);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveMaxPerPage()
    {
        return $this->resolveParameter('max_per_page', 10);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRedirectRoute()
    {
        if (($request = $this->resolveRequest()) === null) {
            throw new RequestNotFoundException();
        }

        $redirectRoute = $this->resolveParameter('redirect_route');

        if (empty($redirectRoute)) {
            throw new RuntimeException(sprintf(
                'The redirect route could not be found for the route "%s".',
                $request->attributes->get('_route')
            ));
        }

        return $redirectRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRedirectRouteParameters($object = null, $forwardParameters = false)
    {
        $routeParameters = $this->resolveRouteParameter('redirect_route_parameters', $object);

        if ($forwardParameters && ($request = $this->resolveRequest()) !== null) {
            return array_replace_recursive($request->query->all(), $routeParameters);
        }

        return $routeParameters;
    }

    /**
     * @return bool
     */
    public function resolveRedirectRouteParametersForward()
    {
        return $this->resolveParameter('redirect_route_parameters_forward', false);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRepositoryMethod($action)
    {
        return $this->resolveParameter('repository_method', 'findFor'.ucfirst(strtolower($action)));
    }

    /**
     * {@inheritdoc}
     */
    public function resolveSerializerGroups()
    {
        return $this->resolveParameter('serializer_groups', []);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveSerializerNull()
    {
        return $this->resolveParameter('serializer_null', true);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveSorting()
    {
        return $this->resolveParameter('sorting', []);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTemplate()
    {
        if (($request = $this->resolveRequest()) === null) {
            throw new RequestNotFoundException();
        }

        $template = $this->resolveParameter('template');

        if (empty($template)) {
            throw new RuntimeException(sprintf(
                'The template could not be found for the route "%s".',
                $request->attributes->get('_route')
            ));
        }

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveThemes()
    {
        return $this->resolveParameter('themes', []);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTranslationDomain()
    {
        return $this->resolveParameter(
            'translation_domain',
            $this->resolveParameter('grid') === null ? 'forms' : 'grids'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValidationGroups(ResourceInterface $resource)
    {
        return $this->resolveParameter('validation_groups', [Constraint::DEFAULT_GROUP, 'lug.'.$resource->getName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveVoter()
    {
        return $this->resolveParameter('voter', false);
    }

    /**
     * @param string $parameter
     * @param mixed  $default
     *
     * @return mixed
     */
    private function resolveParameter($parameter, $default = null)
    {
        if (($request = $this->resolveRequest()) === null) {
            return $default;
        }

        return $request->attributes->get('_lug_'.$parameter, $default);
    }

    /**
     * @return Request|null
     */
    private function resolveRequest()
    {
        return $this->requestStack->getMasterRequest();
    }

    /**
     * @param string      $parameter
     * @param object|null $object
     *
     * @return mixed[]
     */
    private function resolveRouteParameter($parameter, $object = null)
    {
        $propertyPaths = $this->resolveParameter($parameter, []);
        $parameters = [];

        if ($object === null) {
            if (!empty($propertyPaths)) {
                throw new RuntimeException(sprintf(
                    'The route parameters could not be found for the route "%s".',
                    $parameter
                ));
            }

            return $parameters;
        }

        foreach ($propertyPaths as $propertyPath) {
            $parameters[$propertyPath] = $this->propertyAccessor->getValue($object, $propertyPath);
        }

        return $parameters;
    }
}
