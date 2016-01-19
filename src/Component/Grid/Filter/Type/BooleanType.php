<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Filter\Type;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'boolean';
    }

    /**
     * {@inheritdoc}
     */
    protected function process($field, $data, array $options)
    {
        $builder = $options['builder'];

        return $builder->getExpressionBuilder()->eq(
            $builder->getProperty($field),
            $builder->createPlaceholder($field, $data)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function validate($data, array $options)
    {
        return parent::validate($data, $options) && is_bool($data);
    }
}
