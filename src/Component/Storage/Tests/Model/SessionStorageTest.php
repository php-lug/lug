<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Storage\Tests\Model;

use Lug\Component\Storage\Model\SessionStorage;
use Lug\Component\Storage\Model\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SessionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SessionInterface
     */
    private $session;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->session = $this->createSessionMock();
        $this->sessionStorage = new SessionStorage($this->session);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->sessionStorage);
    }

    public function testOffsetExists()
    {
        $this->session
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($offset = 'foo'))
            ->will($this->returnValue(true));

        $this->assertTrue(isset($this->sessionStorage[$offset]));
    }

    public function testOffsetGet()
    {
        $this->session
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo($offset = 'foo'),
                $this->isNull()
            )
            ->will($this->returnValue($value = 'bar'));

        $this->assertSame($value, $this->sessionStorage[$offset]);
    }

    public function testOffsetSet()
    {
        $this->session
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($offset = 'foo'),
                $this->identicalTo($value = 'bar')
            );

        $this->sessionStorage[$offset] = $value;
    }

    public function testOffsetUnset()
    {
        $this->session
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($offset = 'foo'));

        unset($this->sessionStorage[$offset]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SessionInterface
     */
    private function createSessionMock()
    {
        return $this->createMock(SessionInterface::class);
    }
}
