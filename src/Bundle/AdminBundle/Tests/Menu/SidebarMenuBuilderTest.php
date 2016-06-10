<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AdminBundle\Tests\Menu;

use Knp\Menu\FactoryInterface;
use Lug\Bundle\AdminBundle\Menu\SidebarMenuBuilder;
use Lug\Bundle\UiBundle\Menu\AbstractMenuBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SidebarMenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SidebarMenuBuilder
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

        $this->builder = new SidebarMenuBuilder($this->factory, $this->eventDispatcher);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractMenuBuilder::class, $this->builder);
    }

    public function testName()
    {
        $this->assertSame('lug.admin.sidebar', $this->builder->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createFactoryMock()
    {
        return $this->createMock(FactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private function createEventDispatcherMock()
    {
        return $this->createMock(EventDispatcherInterface::class);
    }
}
