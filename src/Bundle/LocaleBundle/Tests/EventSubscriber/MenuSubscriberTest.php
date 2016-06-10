<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\EventSubscriber;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lug\Bundle\LocaleBundle\EventSubscriber\MenuSubscriber;
use Lug\Bundle\UiBundle\Menu\MenuBuilderEvent;
use Lug\Bundle\UiBundle\Menu\MenuBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MenuSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MenuSubscriber
     */
    private $menuSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->menuSubscriber = new MenuSubscriber();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->menuSubscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([MenuBuilderEvents::BUILD => 'onBuild'], MenuSubscriber::getSubscribedEvents());
    }

    public function testBuildWithSidebar()
    {
        $event = $this->createMenuBuilderEventMock();
        $event
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnValue($item = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('lug.admin.sidebar'));

        $event
            ->expects($this->once())
            ->method('getFactory')
            ->will($this->returnValue($factory = $this->createFactoryMock()));

        $factory
            ->expects($this->once())
            ->method('createItem')
            ->with(
                $this->identicalTo('locale'),
                $this->identicalTo([
                    'route'           => 'lug_admin_locale_index',
                    'label'           => 'lug.admin.menu.sidebar.locale',
                    'labelAttributes' => ['icon' => 'language'],
                    'extras'          => ['routes' => [['pattern' => '/^lug_admin_locale_.+$/']]],
                ])
            )
            ->will($this->returnValue($localeChild = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('addChild')
            ->with($this->identicalTo($localeChild));

        $this->menuSubscriber->onBuild($event);
    }

    public function testBuildWithoutSidebar()
    {
        $event = $this->createMenuBuilderEventMock();
        $event
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnValue($item = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $item
            ->expects($this->never())
            ->method('addChild');

        $this->menuSubscriber->onBuild($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MenuBuilderEvent
     */
    private function createMenuBuilderEventMock()
    {
        return $this->createMock(MenuBuilderEvent::class);
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
