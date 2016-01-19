<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Twig;

use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Renderer\RendererInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Pagerfanta\Pagerfanta;
use WhiteOctober\PagerfantaBundle\Twig\PagerfantaExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridExtension extends \Twig_Extension implements RendererInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PagerfantaExtension
     */
    private $pagerfantaExtension;

    /**
     * @param RendererInterface   $renderer
     * @param PagerfantaExtension $pagerfantaExtension
     */
    public function __construct(RendererInterface $renderer, PagerfantaExtension $pagerfantaExtension)
    {
        $this->renderer = $renderer;
        $this->pagerfantaExtension = $pagerfantaExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = ['is_safe' => ['html']];

        return [
            new \Twig_SimpleFunction('lug_grid', [$this, 'render'], $options),
            new \Twig_SimpleFunction('lug_grid_filters', [$this, 'renderFilters'], $options),
            new \Twig_SimpleFunction('lug_grid_body', [$this, 'renderGrid'], $options),
            new \Twig_SimpleFunction('lug_grid_column', [$this, 'renderColumn'], $options),
            new \Twig_SimpleFunction('lug_grid_column_actions', [$this, 'renderColumnActions'], $options),
            new \Twig_SimpleFunction('lug_grid_column_action', [$this, 'renderColumnAction'], $options),
            new \Twig_SimpleFunction('lug_grid_column_sortings', [$this, 'renderColumnSortings'], $options),
            new \Twig_SimpleFunction('lug_grid_column_sorting', [$this, 'renderColumnSorting'], $options),
            new \Twig_SimpleFunction('lug_grid_global_actions', [$this, 'renderGlobalActions'], $options),
            new \Twig_SimpleFunction('lug_grid_global_action', [$this, 'renderGlobalAction'], $options),
            new \Twig_SimpleFunction('lug_grid_pager', [$this, 'renderPager'], $options),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function render(GridViewInterface $grid)
    {
        return $this->renderer->render($grid);
    }

    /**
     * {@inheritdoc}
     */
    public function renderFilters(GridViewInterface $grid)
    {
        return $this->renderer->renderFilters($grid);
    }

    /**
     * {@inheritdoc}
     */
    public function renderGrid(GridViewInterface $grid)
    {
        return $this->renderer->renderGrid($grid);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumn(GridViewInterface $grid, ColumnInterface $column, $data)
    {
        return $this->renderer->renderColumn($grid, $column, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnSortings(GridViewInterface $grid, ColumnInterface $column)
    {
        return $this->renderer->renderColumnSortings($grid, $column);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnSorting(GridViewInterface $grid, ColumnInterface $column, $sorting)
    {
        return $this->renderer->renderColumnSorting($grid, $column, $sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnActions(GridViewInterface $grid, $data)
    {
        return $this->renderer->renderColumnActions($grid, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnAction(GridViewInterface $grid, ActionInterface $action, $data)
    {
        return $this->renderer->renderColumnAction($grid, $action, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderGlobalActions(GridViewInterface $grid)
    {
        return $this->renderer->renderGlobalActions($grid);
    }

    /**
     * {@inheritdoc}
     */
    public function renderGlobalAction(GridViewInterface $grid, ActionInterface $action)
    {
        return $this->renderer->renderGlobalAction($grid, $action);
    }

    /**
     * @param Pagerfanta  $pager
     * @param string|null $name
     * @param mixed[]     $options
     *
     * @return string
     */
    public function renderPager(Pagerfanta $pager, $name = null, array $options = [])
    {
        if (isset($options['routeParams']['grid']['reset'])) {
            unset($options['routeParams']['grid']);
        }

        return $this->pagerfantaExtension->renderPagerfanta($pager, $name, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lug_grid';
    }
}
