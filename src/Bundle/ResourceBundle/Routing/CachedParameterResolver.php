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

use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CachedParameterResolver implements ParameterResolverInterface
{
    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var mixed[]
     */
    private $cache = [];

    /**
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(ParameterResolverInterface $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveApi()
    {
        return !isset($this->cache[$key = 'api'])
            ? $this->cache[$key] = $this->parameterResolver->resolveApi()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCriteria($mandatory = false)
    {
        if (isset($this->cache[$key = 'criteria'])) {
            $criteria = $this->cache[$key];

            if (!empty($criteria) || !$mandatory) {
                return $criteria;
            }
        }

        return $this->cache[$key] = $this->parameterResolver->resolveCriteria($mandatory);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCurrentPage()
    {
        return !isset($this->cache[$key = 'current_page'])
            ? $this->cache[$key] = $this->parameterResolver->resolveCurrentPage()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveForm(ResourceInterface $resource)
    {
        return !isset($this->cache[$key = 'form_'.spl_object_hash($resource)])
            ? $this->cache[$key] = $this->parameterResolver->resolveForm($resource)
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveGrid(ResourceInterface $resource)
    {
        return !isset($this->cache[$key = 'grid_'.spl_object_hash($resource)])
            ? $this->cache[$key] = $this->parameterResolver->resolveGrid($resource)
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHateoas()
    {
        return !isset($this->cache[$key = 'hateoas'])
            ? $this->cache[$key] = $this->parameterResolver->resolveHateoas()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveLocationRoute()
    {
        return !isset($this->cache[$key = 'location_route'])
            ? $this->cache[$key] = $this->parameterResolver->resolveLocationRoute()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveLocationRouteParameters($object)
    {
        return !isset($this->cache[$key = 'location_route_parameters_'.spl_object_hash($object)])
            ? $this->cache[$key] = $this->parameterResolver->resolveLocationRouteParameters($object)
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveMaxPerPage()
    {
        return !isset($this->cache[$key = 'max_per_page'])
            ? $this->cache[$key] = $this->parameterResolver->resolveMaxPerPage()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRedirectRoute()
    {
        return !isset($this->cache[$key = 'redirect_route'])
            ? $this->cache[$key] = $this->parameterResolver->resolveRedirectRoute()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRedirectRouteParameters($object = null, $forwardParameters = false)
    {
        $key = 'redirect_route_parameters';

        if ($object !== null) {
            $key .= '_'.spl_object_hash($object);
        }

        if ($forwardParameters) {
            $key .= '_forward';
        }

        return isset($this->cache[$key])
            ? $this->cache[$key]
            : $this->cache[$key] = $this->parameterResolver->resolveRedirectRouteParameters(
                $object,
                $forwardParameters
            );
    }

    /**
     * @return bool
     */
    public function resolveRedirectRouteParametersForward()
    {
        return !isset($this->cache[$key = 'redirect_route_parameters_forward'])
            ? $this->cache[$key] = $this->parameterResolver->resolveRedirectRouteParametersForward()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRepositoryMethod($action)
    {
        return !isset($this->cache[$key = 'repository_method_'.$action])
            ? $this->cache[$key] = $this->parameterResolver->resolveRepositoryMethod($action)
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveSerializerGroups(ResourceInterface $resource)
    {
        return !isset($this->cache[$key = 'serializer_groups_'.spl_object_hash($resource)])
            ? $this->cache[$key] = $this->parameterResolver->resolveSerializerGroups($resource)
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveSerializerNull()
    {
        return !isset($this->cache[$key = 'serializer_null'])
            ? $this->cache[$key] = $this->parameterResolver->resolveSerializerNull()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveSorting()
    {
        return !isset($this->cache[$key = 'sorting'])
            ? $this->cache[$key] = $this->parameterResolver->resolveSorting()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTemplate()
    {
        return !isset($this->cache[$key = 'template'])
            ? $this->cache[$key] = $this->parameterResolver->resolveTemplate()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveThemes()
    {
        return !isset($this->cache[$key = 'themes'])
            ? $this->cache[$key] = $this->parameterResolver->resolveThemes()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTranslationDomain()
    {
        return !isset($this->cache[$key = 'translation_domain'])
            ? $this->cache[$key] = $this->parameterResolver->resolveTranslationDomain()
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValidationGroups(ResourceInterface $resource)
    {
        return !isset($this->cache[$key = 'validation_groups_'.spl_object_hash($resource)])
            ? $this->cache[$key] = $this->parameterResolver->resolveValidationGroups($resource)
            : $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveVoter()
    {
        return !isset($this->cache[$key = 'voter'])
            ? $this->cache[$key] = $this->parameterResolver->resolveVoter()
            : $this->cache[$key];
    }
}
