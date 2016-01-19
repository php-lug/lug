<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Sort\Type;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function sort($data, array $options)
    {
        $builder = $options['builder'];

        $builder->orderBy($builder->getProperty($options['field']), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'column';
    }
}
