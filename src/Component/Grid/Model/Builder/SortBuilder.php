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
use Lug\Component\Grid\Model\Sort;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SortBuilder implements SortBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(array $config)
    {
        return new Sort(
            $this->buildName($config),
            $this->buildType($config),
            $this->buildOptions($config)
        );
    }

    /**
     * @param mixed[] $config
     *
     * @return string
     */
    protected function buildName(array $config)
    {
        if (!isset($config['name'])) {
            throw new ConfigNotFoundException(sprintf('The sort config "name" could not be found.'));
        }

        return $config['name'];
    }

    /**
     * @param mixed[] $config
     *
     * @return string
     */
    protected function buildType(array $config)
    {
        if (!isset($config['type'])) {
            throw new ConfigNotFoundException(sprintf('The sort config "type" could not be found.'));
        }

        return $config['type'];
    }

    /**
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    protected function buildOptions(array $config)
    {
        switch ($this->buildType($config)) {
            case 'resource':
                if (!isset($config['options']['resource'])) {
                    $config['options']['resource'] = $this->buildName($config);
                }
                break;
        }

        return isset($config['options']) ? $config['options'] : [];
    }
}
