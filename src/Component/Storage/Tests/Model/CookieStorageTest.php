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

use Lug\Component\Storage\Model\CookieStorage;
use Lug\Component\Storage\Model\StorageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CookieStorage
     */
    private $cookieStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $cookieBag;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $request = $this->createRequestMock();
        $this->cookieBag = $request->cookies = $this->createParameterBagMock();

        $requestStack = $this->createRequestStackMock();
        $requestStack
            ->expects($this->atMost(1))
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        $this->cookieStorage = new CookieStorage($requestStack);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->cookieStorage);
    }

    public function testOffsetExists()
    {
        $this->cookieBag
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($offset = 'foo'))
            ->will($this->returnValue(true));

        $this->assertTrue(isset($this->cookieStorage[$offset]));
    }

    public function testOffsetGet()
    {
        $this->cookieBag
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo($offset = 'foo'),
                $this->isNull()
            )
            ->will($this->returnValue($value = 'bar'));

        $this->assertSame($value, $this->cookieStorage[$offset]);
    }

    public function testOffsetSet()
    {
        $this->cookieBag
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($offset = 'foo'),
                $this->identicalTo($value = 'bar')
            );

        $this->cookieStorage[$offset] = $value;
    }

    public function testOffsetUnset()
    {
        $this->cookieBag
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($offset = 'foo'));

        unset($this->cookieStorage[$offset]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->getMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createRequestMock()
    {
        return $this->getMock(Request::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterBag
     */
    private function createParameterBagMock()
    {
        return $this->getMock(ParameterBag::class);
    }
}
