<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Model\Builder;

use Lug\Component\Grid\Exception\ConfigNotFoundException;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Grid\Model\Grid;
use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBuilder implements GridBuilderInterface
{
    /**
     * @var ColumnBuilderInterface
     */
    private $columnBuilder;

    /**
     * @var FilterBuilderInterface
     */
    private $filterBuilder;

    /**
     * @var SortBuilderInterface
     */
    private $sortBuilder;

    /**
     * @var ActionBuilderInterface
     */
    private $actionBuilder;

    /**
     * @var BatchBuilderInterface
     */
    private $batchBuilder;

    /**
     * @param ColumnBuilderInterface $columnBuilder
     * @param FilterBuilderInterface $filterBuilder
     * @param SortBuilderInterface   $sortBuilder
     * @param ActionBuilderInterface $actionBuilder
     * @param BatchBuilderInterface  $batchBuilder
     */
    public function __construct(
        ColumnBuilderInterface $columnBuilder,
        FilterBuilderInterface $filterBuilder,
        SortBuilderInterface $sortBuilder,
        ActionBuilderInterface $actionBuilder,
        BatchBuilderInterface $batchBuilder
    ) {
        $this->columnBuilder = $columnBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortBuilder = $sortBuilder;
        $this->actionBuilder = $actionBuilder;
        $this->batchBuilder = $batchBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $config)
    {
        $config = $this->prepareConfig($config);

        return new Grid(
            $this->buildResource($config),
            $this->buildColumns($config),
            $this->buildFilters($config),
            $this->buildSorts($config),
            $this->buildGlobalActions($config),
            $this->buildColumnActions($config),
            $this->buildBatches($config),
            $this->buildData($config),
            $this->buildOptions($config)
        );
    }

    /**
     * @param mixed[] $config
     *
     * @return ResourceInterface
     */
    protected function buildResource(array $config)
    {
        if (!isset($config['resource'])) {
            throw new ConfigNotFoundException(sprintf('The grid config "resource" could not be found.'));
        }

        return $config['resource'];
    }

    /**
     * @param mixed[] $config
     *
     * @return ColumnInterface[]
     */
    protected function buildColumns(array $config)
    {
        $columns = [];

        foreach (isset($config['columns']) ? $config['columns'] : [] as $name => $column) {
            $column = $this->buildColumn(array_merge(['name' => $name], $column), $config);
            $columns[$column->getName()] = $column;
        }

        return $columns;
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return ColumnInterface
     */
    protected function buildColumn(array $config, array $parentConfig)
    {
        return $this->columnBuilder->build($this->prepareConfig($config, $parentConfig));
    }

    /**
     * @param mixed[] $config
     *
     * @return FilterInterface[]
     */
    protected function buildFilters(array $config)
    {
        $filters = [];

        foreach ((isset($config['filters']) ? $config['filters'] : []) as $name => $filter) {
            $filter = $this->buildFilter(array_merge(['name' => $name], $filter), $config);
            $filters[$filter->getName()] = $filter;
        }

        return $filters;
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return FilterInterface
     */
    protected function buildFilter(array $config, array $parentConfig)
    {
        return $this->filterBuilder->build($this->prepareConfig($config, $parentConfig));
    }

    /**
     * @param mixed[] $config
     *
     * @return SortInterface[]
     */
    protected function buildSorts(array $config)
    {
        $sorts = [];

        foreach ((isset($config['sorts']) ? $config['sorts'] : []) as $name => $sort) {
            $sort = $this->buildSort(array_merge(['name' => $name], $sort), $config);
            $sorts[$sort->getName()] = $sort;
        }

        return $sorts;
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return SortInterface
     */
    protected function buildSort(array $config, array $parentConfig)
    {
        return $this->sortBuilder->build($this->prepareConfig($config, $parentConfig));
    }

    /**
     * @param mixed[] $config
     *
     * @return ActionInterface[]
     */
    protected function buildGlobalActions(array $config)
    {
        $globalActions = [];

        foreach ((isset($config['global_actions']) ? $config['global_actions'] : []) as $name => $action) {
            $action = $this->buildGlobalAction(array_merge(['name' => $name], $action), $config);
            $globalActions[$action->getName()] = $action;
        }

        return $globalActions;
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return ActionInterface
     */
    protected function buildGlobalAction(array $config, array $parentConfig)
    {
        return $this->actionBuilder->build($this->prepareConfig($config, $parentConfig));
    }

    /**
     * @param mixed[] $config
     *
     * @return ActionInterface[]
     */
    protected function buildColumnActions(array $config)
    {
        $columnActions = [];

        foreach ((isset($config['column_actions']) ? $config['column_actions'] : []) as $name => $action) {
            $action = $this->buildColumnAction(array_merge(['name' => $name], $action), $config);
            $columnActions[$action->getName()] = $action;
        }

        return $columnActions;
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return ActionInterface
     */
    protected function buildColumnAction(array $config, array $parentConfig)
    {
        return $this->actionBuilder->build($this->prepareConfig($config, $parentConfig));
    }

    /**
     * @param mixed[] $config
     *
     * @return BatchInterface[]
     */
    protected function buildBatches(array $config)
    {
        $batches = [];

        foreach ((isset($config['batches']) ? $config['batches'] : []) as $name => $batch) {
            $batch = $this->buildBatch(array_merge(['name' => $name], $batch), $config);
            $batches[$batch->getName()] = $batch;
        }

        return $batches;
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return BatchInterface
     */
    protected function buildBatch(array $config, array $parentConfig)
    {
        return $this->batchBuilder->build($this->prepareConfig($config, $parentConfig));
    }

    /**
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    protected function buildData(array $config)
    {
        return isset($config['data']) ? $config['data'] : [];
    }

    /**
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    protected function buildOptions(array $config)
    {
        return isset($config['options']) ? $config['options'] : [];
    }

    /**
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return mixed[]
     */
    protected function prepareConfig(array $config, array $parentConfig = [])
    {
        return $config;
    }
}
