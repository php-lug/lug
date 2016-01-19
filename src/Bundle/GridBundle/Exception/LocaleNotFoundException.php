<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Exception;

use Lug\Component\Grid\Exception\ExceptionInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleNotFoundException extends \RuntimeException implements ExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message ?: 'The locale could not be found.', $code, $previous);
    }
}
