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
use Lug\Component\Grid\Model\Batch;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchBuilder implements BatchBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(array $config)
    {
        return new Batch(
            $this->buildName($config),
            $this->buildLabel($config),
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
            throw new ConfigNotFoundException(sprintf('The batch config "name" could not be found.'));
        }

        return $config['name'];
    }

    /**
     * @param mixed[] $config
     *
     * @return string
     */
    protected function buildLabel(array $config)
    {
        return isset($config['label']) ? $config['label'] : $this->buildName($config);
    }

    /**
     * @param mixed[] $config
     *
     * @return string
     */
    protected function buildType(array $config)
    {
        if (!isset($config['type'])) {
            throw new ConfigNotFoundException(sprintf('The batch config "type" could not be found.'));
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
        return isset($config['options']) ? $config['options'] : [];
    }
}
