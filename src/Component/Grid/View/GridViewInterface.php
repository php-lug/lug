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

use Lug\Component\Grid\DataSource\DataSourceInterface;
use Lug\Component\Grid\Model\GridInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface GridViewInterface
{
    /**
     * @return GridInterface
     */
    public function getDefinition();

    /**
     * @param mixed[] $options
     *
     * @return DataSourceInterface
     */
    public function getDataSource(array $options = []);
}
