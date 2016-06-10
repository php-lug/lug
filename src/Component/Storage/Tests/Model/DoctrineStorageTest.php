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

use Doctrine\Common\Cache\Cache;
use Lug\Component\Storage\Model\DoctrineStorage;
use Lug\Component\Storage\Model\StorageInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DoctrineStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineStorage
     */
    private $doctrineCacheStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Cache
     */
    private $cache;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cache = $this->createCacheMock();
        $this->doctrineCacheStorage = new DoctrineStorage($this->cache);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->doctrineCacheStorage);
    }

    public function testOffsetExists()
    {
        $this->cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($offset = 'foo'))
            ->will($this->returnValue(true));

        $this->assertTrue(isset($this->doctrineCacheStorage[$offset]));
    }

    public function testOffsetExistsWithExistentEntry()
    {
        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->with($this->identicalTo($offset = 'foo'))
            ->will($this->returnValue($value = 'bar'));

        $this->assertSame($value, $this->doctrineCacheStorage[$offset]);
    }

    public function testOffsetExistsWithNonexistentEntry()
    {
        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->with($this->identicalTo($offset = 'foo'))
            ->will($this->returnValue(false));

        $this->assertNull($this->doctrineCacheStorage[$offset]);
    }

    public function testOffsetSet()
    {
        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->identicalTo($offset = 'foo'),
                $this->identicalTo($value = 'bar')
            );

        $this->doctrineCacheStorage[$offset] = $value;
    }

    public function testOffsetUnset()
    {
        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($offset = 'foo'));

        unset($this->doctrineCacheStorage[$offset]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cache
     */
    private function createCacheMock()
    {
        return $this->createMock(Cache::class);
    }
}
