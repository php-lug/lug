<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @param int             $statusCode
     * @param string|null     $message
     * @param int             $code
     * @param \Exception|null $e
     */
    public function __construct(
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        $message = null,
        $code = 0,
        \Exception $e = null
    ) {
        parent::__construct($message, $code, $e);

        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
