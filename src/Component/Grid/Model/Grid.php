<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Model;

use Lug\Component\Grid\Exception\InvalidArgumentException;
use Lug\Component\Grid\Exception\OptionNotFoundException;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Grid implements GridInterface
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var ColumnInterface[]
     */
    private $columns;

    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @var SortInterface[]
     */
    private $sorts;

    /**
     * @var ActionInterface[]
     */
    private $globalActions;

    /**
     * @var ActionInterface[]
     */
    private $columnActions;

    /**
     * @var BatchInterface[]
     */
    private $batches;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var mixed[]
     */
    private $options;

    /**
     * @param ResourceInterface $resource
     * @param ColumnInterface[] $columns
     * @param FilterInterface[] $filters
     * @param SortInterface[]   $sorts
     * @param ActionInterface[] $globalActions
     * @param ActionInterface[] $columnActions
     * @param BatchInterface[]  $batches
     * @param mixed[]           $data
     * @param mixed[]           $options
     */
    public function __construct(
        ResourceInterface $resource,
        array $columns = [],
        array $filters = [],
        array $sorts = [],
        array $globalActions = [],
        array $columnActions = [],
        array $batches = [],
        array $data = [],
        array $options = []
    ) {
        $this->resource = $resource;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->sorts = $sorts;
        $this->globalActions = $globalActions;
        $this->columnActions = $columnActions;
        $this->batches = $batches;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumns()
    {
        return !empty($this->columns);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if (!$this->hasColumn($name)) {
            throw new InvalidArgumentException(sprintf('The column "%s" could not be found.', $name));
        }

        return $this->columns[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilters()
    {
        return !empty($this->filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilter($name)
    {
        return isset($this->filters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter($name)
    {
        if (!$this->hasFilter($name)) {
            throw new InvalidArgumentException(sprintf('The filter "%s" could not be found.', $name));
        }

        return $this->filters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasSorts()
    {
        return !empty($this->sorts);
    }

    /**
     * {@inheritdoc}
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSort($name)
    {
        return isset($this->sorts[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSort($name)
    {
        if (!$this->hasSort($name)) {
            throw new InvalidArgumentException(sprintf('The sort "%s" could not be found.', $name));
        }

        return $this->sorts[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasGlobalActions()
    {
        return !empty($this->globalActions);
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalActions()
    {
        return $this->globalActions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGlobalAction($name)
    {
        return isset($this->globalActions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalAction($name)
    {
        if (!$this->hasGlobalAction($name)) {
            throw new InvalidArgumentException(sprintf('The global action "%s" could not be found.', $name));
        }

        return $this->globalActions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnActions()
    {
        return !empty($this->columnActions);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnActions()
    {
        return $this->columnActions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnAction($name)
    {
        return isset($this->columnActions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAction($name)
    {
        if (!$this->hasColumnAction($name)) {
            throw new InvalidArgumentException(sprintf('The column action "%s" could not be found.', $name));
        }

        return $this->columnActions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasBatches()
    {
        return !empty($this->batches);
    }

    /**
     * {@inheritdoc}
     */
    public function getBatches()
    {
        return $this->batches;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBatch($name)
    {
        return isset($this->batches[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBatch($name)
    {
        if (!$this->hasBatch($name)) {
            throw new InvalidArgumentException(sprintf('The batch "%s" could not be found.', $name));
        }

        return $this->batches[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasData()
    {
        return !empty($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOptions()
    {
        return !empty($this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new OptionNotFoundException(sprintf('The grid option "%s" could not be found.', $name));
        }

        return $this->options[$name];
    }
}
