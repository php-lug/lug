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

use Lug\Component\Grid\Model\ColumnInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ColumnBuilderInterface
{
    /**
     * @param mixed[] $config
     *
     * @return ColumnInterface
     */
    public function build(array $config);
}
