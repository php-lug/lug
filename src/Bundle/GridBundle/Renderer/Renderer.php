<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Renderer;

use Lug\Component\Grid\Action\ActionRendererInterface;
use Lug\Component\Grid\Column\ColumnRendererInterface;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Renderer\RendererInterface;
use Lug\Component\Grid\Sort\SorterRendererInterface;
use Lug\Component\Grid\View\GridViewInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Renderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ActionRendererInterface
     */
    private $actionRenderer;

    /**
     * @var ColumnRendererInterface
     */
    private $columnRenderer;

    /**
     * @var SorterRendererInterface
     */
    private $sorterRenderer;

    /**
     * @var string[]
     */
    private $templates;

    /**
     * @param \Twig_Environment       $twig
     * @param ActionRendererInterface $actionRenderer
     * @param ColumnRendererInterface $columnRenderer
     * @param SorterRendererInterface $sorterRenderer
     * @param string[]                $templates
     */
    public function __construct(
        \Twig_Environment $twig,
        ActionRendererInterface $actionRenderer,
        ColumnRendererInterface $columnRenderer,
        SorterRendererInterface $sorterRenderer,
        array $templates = []
    ) {
        $this->twig = $twig;
        $this->actionRenderer = $actionRenderer;
        $this->columnRenderer = $columnRenderer;
        $this->sorterRenderer = $sorterRenderer;
        $this->templates = $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function render(GridViewInterface $grid)
    {
        return $this->doRender($grid, 'grid');
    }

    /**
     * {@inheritdoc}
     */
    public function renderFilters(GridViewInterface $grid)
    {
        return $this->doRender($grid, 'filters');
    }

    /**
     * {@inheritdoc}
     */
    public function renderGrid(GridViewInterface $grid)
    {
        return $this->doRender($grid, 'body');
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumn(GridViewInterface $grid, ColumnInterface $column, $data)
    {
        return $this->doRender($grid, 'column', [
            'column' => $column,
            'data'   => $data,
            'value'  => $this->columnRenderer->render($grid, $column, $data),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnSortings(GridViewInterface $grid, ColumnInterface $column)
    {
        return $this->doRender($grid, 'column_sortings', ['column' => $column]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnSorting(GridViewInterface $grid, ColumnInterface $column, $sorting)
    {
        return $this->doRender($grid, 'column_sorting', [
            'column'  => $column,
            'sorting' => $sorting,
            'label'   => 'lug.sorting.'.strtolower($sorting),
            'value'   => $this->sorterRenderer->render($grid, $column, $sorting),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnActions(GridViewInterface $grid, $data)
    {
        return $this->doRender($grid, 'column_actions', ['data' => $data]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderColumnAction(GridViewInterface $grid, ActionInterface $action, $data)
    {
        return $this->doRender($grid, 'column_action', [
            'action' => $action,
            'data'   => $data,
            'value'  => $this->actionRenderer->render($grid, $action, $data),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderGlobalActions(GridViewInterface $grid)
    {
        return $this->doRender($grid, 'global_actions');
    }

    /**
     * {@inheritdoc}
     */
    public function renderGlobalAction(GridViewInterface $grid, ActionInterface $action)
    {
        return $this->doRender($grid, 'global_action', [
            'action' => $action,
            'value'  => $this->actionRenderer->render($grid, $action, null),
        ]);
    }

    /**
     * @param GridViewInterface $grid
     * @param string            $template
     * @param mixed[]           $context
     *
     * @return string
     */
    private function doRender(GridViewInterface $grid, $template, array $context = [])
    {
        $context['grid'] = $grid;

        return $this->twig->render($this->resolveTemplate($grid->getDefinition(), $template), $context);
    }

    /**
     * @param GridInterface $grid
     * @param string        $template
     *
     * @return string
     */
    private function resolveTemplate(GridInterface $grid, $template)
    {
        if ($grid->hasOption($option = $template.'_template')) {
            return $grid->getOption($option);
        }

        if (isset($this->templates[$template])) {
            return $this->templates[$template];
        }

        return '@LugGrid/'.$template.'.html.twig';
    }
}
