<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Registry;

use Lug\Component\Registry\Model\ServiceRegistry;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Registry\RepositoryRegistry;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RepositoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->repositoryRegistry = new RepositoryRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ServiceRegistryInterface::class, $this->repositoryRegistry);
        $this->assertInstanceOf(ServiceRegistry::class, $this->repositoryRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->repositoryRegistry));
    }

    public function testInitialState()
    {
        $this->repositoryRegistry = new RepositoryRegistry([$key = 'foo' => $value = $this->createRepositoryMock()]);

        $this->assertSame($value, $this->repositoryRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->getMock(RepositoryInterface::class);
    }
}
