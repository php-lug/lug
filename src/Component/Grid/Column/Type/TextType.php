<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column\Type;

use Lug\Component\Grid\Exception\InvalidTypeException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        $text = $this->getValue($data, $options);

        if ($text === null) {
            return;
        }

        if (!is_scalar($text)) {
            throw new InvalidTypeException(sprintf(
                'The "%s" %s column type expects a scalar value, got "%s".',
                $options['column']->getName(),
                $this->getName(),
                is_object($text) ? get_class($text) : gettype($text)
            ));
        }

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'text';
    }
}
