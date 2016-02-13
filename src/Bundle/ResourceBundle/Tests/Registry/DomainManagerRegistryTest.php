<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Registry;

use Lug\Component\Registry\Model\Registry;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Domain\DomainManagerInterface;
use Lug\Component\Resource\Registry\DomainManagerRegistry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainManagerRegistry
     */
    private $domainManagerRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->domainManagerRegistry = new DomainManagerRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegistryInterface::class, $this->domainManagerRegistry);
        $this->assertInstanceOf(Registry::class, $this->domainManagerRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->domainManagerRegistry));
    }

    public function testInitialState()
    {
        $this->domainManagerRegistry = new DomainManagerRegistry([
            $key = 'foo' => $value = $this->createDomainManagerMock(),
        ]);

        $this->assertSame($value, $this->domainManagerRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainManagerInterface
     */
    private function createDomainManagerMock()
    {
        return $this->getMock(DomainManagerInterface::class);
    }
}
