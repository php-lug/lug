<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\Tests\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lug\Bundle\UiBundle\Menu\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MenuBuilderEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MenuBuilderEvent
     */
    private $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ItemInterface
     */
    private $item;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->factory = $this->createFactoryMock();
        $this->item = $this->createItemMock();

        $this->event = new MenuBuilderEvent($this->factory, $this->item);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Event::class, $this->event);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->factory, $this->event->getFactory());
        $this->assertSame($this->item, $this->event->getItem());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createFactoryMock()
    {
        return $this->createMock(FactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ItemInterface
     */
    private function createItemMock()
    {
        return $this->createMock(ItemInterface::class);
    }
}
