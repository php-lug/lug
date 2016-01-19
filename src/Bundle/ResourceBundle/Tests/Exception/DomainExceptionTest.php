<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Exception;

use Lug\Component\Resource\Exception\DomainException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainException
     */
    private $domainException;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->domainException = new DomainException();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->domainException);
    }

    public function testDefaultState()
    {
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->domainException->getStatusCode());
        $this->assertEmpty($this->domainException->getMessage());
        $this->assertEmpty($this->domainException->getCode());
        $this->assertNull($this->domainException->getPrevious());
    }

    public function testInitialState()
    {
        $this->domainException = new DomainException(
            $statusCode = Response::HTTP_BAD_REQUEST,
            $message = 'message',
            $code = 123,
            $previous = new \Exception()
        );

        $this->assertSame($statusCode, $this->domainException->getStatusCode());
        $this->assertSame($message, $this->domainException->getMessage());
        $this->assertSame($code, $this->domainException->getCode());
        $this->assertSame($previous, $this->domainException->getPrevious());
    }
}
