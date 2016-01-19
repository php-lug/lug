<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\DataTransformer;

use Lug\Component\Grid\Sort\SorterInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridSortingTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(sprintf(
                'The grid sorting value should be an array, got "%s".',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $sorting = [];

        foreach ($value as $key => $order) {
            if (!in_array($order, [SorterInterface::ASC, SorterInterface::DESC], true)) {
                throw new TransformationFailedException(sprintf(
                    'The grid sorting order should be "ASC" or "DESC", got "%s".',
                    $order
                ));
            }

            if ($order === SorterInterface::DESC) {
                $key = '-'.$key;
            }

            $sorting[] = $key;
        }

        return implode(',', $sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $exploded = explode(',', $value);

        if ($exploded === false) {
            throw new TransformationFailedException(sprintf(
                'The grid sorting should be comma-separated, got "%s".',
                $value
            ));
        }

        $sorting = [];

        foreach (array_map('trim', $exploded) as $order) {
            if (empty($order)) {
                continue;
            }

            if ($order[0] === '-') {
                $sorting[substr($order, 1)] = SorterInterface::DESC;
            } else {
                $sorting[$order] = SorterInterface::ASC;
            }
        }

        return $sorting;
    }
}
