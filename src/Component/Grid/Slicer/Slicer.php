<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Slicer;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Slicer implements SlicerInterface
{
    /**
     * {@inheritdoc}
     */
    public function slice(DataSourceBuilderInterface $builder, GridInterface $grid, array $slicing)
    {
        $limit = isset($slicing['limit'])
            ? $slicing['limit']
            : ($grid->hasOption('limit') ? $grid->getOption('limit') : 10);

        $page = isset($slicing['page'])
            ? $slicing['page']
            : ($grid->hasOption('page') ? $grid->getOption('page') : 1);

        $builder
            ->setLimit($limit)
            ->setPage($page);
    }
}
