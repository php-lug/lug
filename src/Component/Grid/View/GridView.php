<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\View;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\DataSource\DataSourceInterface;
use Lug\Component\Grid\Model\GridInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridView implements GridViewInterface
{
    /**
     * @var GridInterface
     */
    private $definition;

    /**
     * @var DataSourceBuilderInterface
     */
    private $dataSourceBuilder;

    /**
     * @var DataSourceInterface[]
     */
    private $cache = [];

    /**
     * @param GridInterface              $definition
     * @param DataSourceBuilderInterface $dataSourceBuilder
     */
    public function __construct(GridInterface $definition, DataSourceBuilderInterface $dataSourceBuilder)
    {
        $this->definition = $definition;
        $this->dataSourceBuilder = $dataSourceBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $options = [])
    {
        if ($this->definition !== null) {
            $options = array_merge($this->definition->getOptions(), $options);
        }

        ksort($options);

        return isset($this->cache[$hash = sha1(json_encode($options))])
            ? $this->cache[$hash]
            : $this->cache[$hash] = $this->dataSourceBuilder->createDataSource($options);
    }
}
