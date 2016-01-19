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
use Lug\Bundle\UiBundle\Menu\AbstractMenuBuilder;
use Lug\Bundle\UiBundle\Menu\MenuBuilderEvent;
use Lug\Bundle\UiBundle\Menu\MenuBuilderEvents;
use Lug\Bundle\UiBundle\Menu\MenuBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbstractMenuBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->factory = $this->createFactoryMock();
        $this->eventDispatcher = $this->createEventDispatcherMock();

        $this->builder = $this->getMockBuilder(AbstractMenuBuilder::class)
            ->setConstructorArgs([$this->factory, $this->eventDispatcher])
            ->getMockForAbstractClass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(MenuBuilderInterface::class, $this->builder);
    }

    public function testCreate()
    {
        $this->builder
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($item = $this->createItemMock()));

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->identicalTo(MenuBuilderEvents::BUILD),
                $this->callback(function (MenuBuilderEvent $event) use ($item) {
                    return $event->getFactory() === $this->factory
                        && $event->getItem() === $item;
                })
            );

        $this->assertSame($item, $this->builder->create());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createFactoryMock()
    {
        return $this->getMock(FactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private function createEventDispatcherMock()
    {
        return $this->getMock(EventDispatcherInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ItemInterface
     */
    private function createItemMock()
    {
        return $this->getMock(ItemInterface::class);
    }
}
