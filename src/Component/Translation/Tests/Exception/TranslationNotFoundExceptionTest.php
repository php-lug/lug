<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Tests\Exception;

use Lug\Component\Translation\Exception\RuntimeException;
use Lug\Component\Translation\Exception\TranslationNotFoundException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslationNotFoundException
     */
    private $exception;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exception = new TranslationNotFoundException();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->exception);
    }

    public function testDefaultState()
    {
        $this->assertSame('The translation could not be found.', $this->exception->getMessage());
        $this->assertSame(0, $this->exception->getCode());
        $this->assertNull($this->exception->getPrevious());
    }

    public function testInitialState()
    {
        $this->exception = new TranslationNotFoundException(
            $message = 'foo',
            $code = 123,
            $previous = new \Exception()
        );

        $this->assertSame($message, $this->exception->getMessage());
        $this->assertSame($code, $this->exception->getCode());
        $this->assertSame($previous, $this->exception->getPrevious());
    }
}
