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

use Doctrine\Common\Persistence\ObjectManager;
use Lug\Component\Registry\Model\Registry;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Registry\ManagerRegistry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->managerRegistry = new ManagerRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegistryInterface::class, $this->managerRegistry);
        $this->assertInstanceOf(Registry::class, $this->managerRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->managerRegistry));
    }

    public function testInitialState()
    {
        $this->managerRegistry = new ManagerRegistry([$key = 'foo' => $value = $this->createObjectManagerMock()]);

        $this->assertSame($value, $this->managerRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    private function createObjectManagerMock()
    {
        return $this->createMock(ObjectManager::class);
    }
}
