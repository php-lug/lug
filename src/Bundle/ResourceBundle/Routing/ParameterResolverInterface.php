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
interface ParameterResolverInterface
{
    /**
     * @return bool
     */
    public function resolveApi();

    /**
     * @param bool $mandatory
     *
     * @return string[]
     */
    public function resolveCriteria($mandatory = false);

    /**
     * @return int
     */
    public function resolveCurrentPage();

    /**
     * @param ResourceInterface $resource
     *
     * @return string
     */
    public function resolveForm(ResourceInterface $resource);

    /**
     * @param ResourceInterface $resource
     *
     * @return mixed[]
     */
    public function resolveGrid(ResourceInterface $resource);

    /**
     * @return bool
     */
    public function resolveHateoas();

    /**
     * @return string
     */
    public function resolveLocationRoute();

    /**
     * @param object $object
     *
     * @return mixed[]
     */
    public function resolveLocationRouteParameters($object);

    /**
     * @return int
     */
    public function resolveMaxPerPage();

    /**
     * @return string
     */
    public function resolveRedirectRoute();

    /**
     * @param object|null $object
     * @param bool        $forwardParameters
     *
     * @return mixed[]
     */
    public function resolveRedirectRouteParameters($object = null, $forwardParameters = false);

    /**
     * @return mixed[]
     */
    public function resolveRedirectRouteParametersForward();

    /**
     * @param string $action
     *
     * @return string
     */
    public function resolveRepositoryMethod($action);

    /**
     * @return string[]
     */
    public function resolveSerializerGroups();

    /**
     * @return bool
     */
    public function resolveSerializerNull();

    /**
     * @return string[]
     */
    public function resolveSorting();

    /**
     * @return string
     */
    public function resolveTemplate();

    /**
     * @return string[]
     */
    public function resolveThemes();

    /**
     * @return string|null
     */
    public function resolveTranslationDomain();

    /**
     * @return string|null
     */
    public function resolveValidationGroups();

    /**
     * @return bool
     */
    public function resolveVoter();
}
