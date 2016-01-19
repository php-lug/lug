<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Tests\Exception;

use Lug\Component\Assetic\Exception\ExceptionInterface;
use Lug\Component\Assetic\Exception\InvalidStrategyException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class InvalidStrategyExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InvalidStrategyException
     */
    private $exception;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exception = new InvalidStrategyException();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(\InvalidArgumentException::class, $this->exception);
        $this->assertInstanceOf(ExceptionInterface::class, $this->exception);
    }

    public function testDefaultState()
    {
        $this->assertSame('', $this->exception->getMessage());
        $this->assertSame(0, $this->exception->getCode());
        $this->assertNull($this->exception->getPrevious());
    }

    public function testInitialState()
    {
        $this->exception = new InvalidStrategyException(
            $message = 'foo',
            $code = 123,
            $previous = new \Exception()
        );

        $this->assertSame($message, $this->exception->getMessage());
        $this->assertSame($code, $this->exception->getCode());
        $this->assertSame($previous, $this->exception->getPrevious());
    }
}
