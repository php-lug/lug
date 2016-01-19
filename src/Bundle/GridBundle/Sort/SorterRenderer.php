<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Sort;

use Lug\Bundle\GridBundle\Filter\FilterManagerInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Sort\SorterInterface;
use Lug\Component\Grid\Sort\SorterRendererInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SorterRenderer implements SorterRendererInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param RequestStack           $requestStack
     * @param FilterManagerInterface $filterManager
     * @param UrlGeneratorInterface  $urlGenerator
     */
    public function __construct(
        RequestStack $requestStack,
        FilterManagerInterface $filterManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->requestStack = $requestStack;
        $this->filterManager = $filterManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function render(GridViewInterface $grid, ColumnInterface $column, $sorting)
    {
        $definition = $grid->getDefinition();
        $name = $column->getName();

        if (!$definition->hasSort($name)) {
            return;
        }

        $sort = $sorting === SorterInterface::ASC ? $name : '-'.$name;
        $routeParameters = [];

        if (($request = $this->requestStack->getMasterRequest()) !== null) {
            $routeParameters = array_merge($request->attributes->get('_route_params', []), $request->query->all());
        }

        if (!isset($routeParameters['grid']['reset'])
            && isset($routeParameters['grid']['sorting'])
            && $routeParameters['grid']['sorting'] === $sort) {
            return;
        }

        if ($definition->hasOption('persistent') && $definition->getOption('persistent')) {
            $filters = $this->filterManager->get($definition);

            if (isset($filters['sorting']) && $filters['sorting'] === $sort) {
                return;
            }
        }

        $routeParameters['grid']['sorting'] = $sort;
        unset($routeParameters['grid']['reset']);

        return $this->urlGenerator->generate($definition->getOption('grid_route'), $routeParameters);
    }
}
