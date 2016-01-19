<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\DataTransformer\Filter;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanFilterTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value === null) {
            return;
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        throw new TransformationFailedException(sprintf(
            'The boolean model data should be a boolean or null, got "%s".',
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return;
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        throw new TransformationFailedException(sprintf(
            'The boolean view data should be "true" or "false", got "%s".',
            is_object($value) ? get_class($value) : (is_string($value) ? $value : gettype($value))
        ));
    }
}
