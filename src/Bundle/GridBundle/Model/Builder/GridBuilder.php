<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Model\Builder;

use Lug\Component\Grid\Model\Builder\GridBuilder as BaseGridBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBuilder extends BaseGridBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function buildColumn(array $config, array $parentConfig)
    {
        return parent::buildColumn(array_merge(
            ['label' => $this->prepareLabel('column', $config, $parentConfig)],
            $config
        ), $parentConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildFilter(array $config, array $parentConfig)
    {
        return parent::buildFilter(array_merge(
            ['label' => $this->prepareLabel('filter', $config, $parentConfig)],
            $config
        ), $parentConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildColumnAction(array $config, array $parentConfig)
    {
        return parent::buildColumnAction(array_merge(
            ['label' => $this->prepareLabel('column_action', $config, $parentConfig)],
            $config
        ), $parentConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildGlobalAction(array $config, array $parentConfig)
    {
        return parent::buildGlobalAction(array_merge(
            ['label' => $this->prepareLabel('global_action', $config, $parentConfig)],
            $config
        ), $parentConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildBatch(array $config, array $parentConfig)
    {
        return parent::buildBatch(array_merge(
            ['label' => $this->prepareLabel('batch', $config, $parentConfig)],
            $config
        ), $parentConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareConfig(array $config, array $parentConfig = [])
    {
        $config['options'] = array_merge(
            $default = ['trans_domain' => 'grids'],
            isset($parentConfig['options']) ? array_intersect_key($parentConfig['options'], $default) : [],
            isset($config['options']) ? $config['options'] : []
        );

        return parent::prepareConfig($config, $parentConfig);
    }

    /**
     * @param string  $type
     * @param mixed[] $config
     * @param mixed[] $parentConfig
     *
     * @return string
     */
    protected function prepareLabel($type, array $config, array $parentConfig)
    {
        return $label = isset($config['label'])
            ? $config['label']
            : 'lug.'.$this->buildResource($parentConfig)->getName().'.'.$type.'.'.$config['name'];
    }
}
