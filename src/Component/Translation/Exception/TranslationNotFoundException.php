<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Exception;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationNotFoundException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message ?: 'The translation could not be found.', $code, $previous);
    }
}
