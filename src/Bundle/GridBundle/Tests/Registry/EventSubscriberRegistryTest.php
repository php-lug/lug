<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Registry;

use Lug\Bundle\GridBundle\Registry\EventSubscriberRegistry;
use Lug\Component\Registry\Model\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class EventSubscriberRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventSubscriberRegistry
     */
    private $eventSubscriberRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->eventSubscriberRegistry = new EventSubscriberRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->eventSubscriberRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->eventSubscriberRegistry));
    }

    public function testInitialState()
    {
        $this->eventSubscriberRegistry = new EventSubscriberRegistry([
            $key = 'foo' => $value = $this->createEventSubscriberMock(),
        ]);

        $this->assertSame($value, $this->eventSubscriberRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventSubscriberInterface
     */
    private function createEventSubscriberMock()
    {
        return $this->createMock(EventSubscriberInterface::class);
    }
}
