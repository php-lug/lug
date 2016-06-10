<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AdminBundle\Tests\DependencyInjection;

use Knp\Menu\FactoryInterface;
use Lug\Bundle\AdminBundle\Controller\DashboardController;
use Lug\Bundle\AdminBundle\DependencyInjection\LugAdminExtension;
use Lug\Bundle\AdminBundle\Menu\SidebarMenuBuilder;
use Lug\Bundle\AdminBundle\Menu\ToolbarMenuBuilder;
use Lug\Bundle\GridBundle\LugGridBundle;
use Lug\Bundle\UiBundle\Menu\AbstractMenuBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugAdminExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugAdminExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $menuFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->menuFactory = $this->createMenuFactoryMock();
        $this->eventDispatcher = $this->createEventDispatcherMock();
        $this->extension = new LugAdminExtension();

        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.bundles', ['LugGridBundle' => LugGridBundle::class]);
        $this->container->set($menuFactoryName = 'knp_menu.factory', $this->menuFactory);
        $this->container->set($eventDispatcherName = 'event_dispatcher', $this->eventDispatcher);

        $this->container->setDefinition('lug.ui.menu.builder', (new Definition(AbstractMenuBuilder::class, [
            new Reference($menuFactoryName),
            new Reference($eventDispatcherName),
        ]))->setAbstract(true));

        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testController()
    {
        $this->compileContainer();

        $this->assertInstanceOf(DashboardController::class, $this->container->get('lug.admin.controller.dashboard'));
    }

    public function testMenus()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($sidebarMenuBuilderName = 'lug.admin.menu.builder.sidebar'));
        $this->assertTrue($this->container->hasDefinition($toolbarMenuBuilderName = 'lug.admin.menu.builder.toolbar'));

        $this->assertSame(
            [['alias' => 'lug.admin.sidebar']],
            $this->container->getDefinition($sidebarMenuBuilderName)->getTag($tag = 'lug.menu.builder')
        );

        $this->assertSame(
            [['alias' => 'lug.admin.toolbar']],
            $this->container->getDefinition($toolbarMenuBuilderName)->getTag($tag)
        );

        $this->assertInstanceOf(SidebarMenuBuilder::class, $this->container->get($sidebarMenuBuilderName));
        $this->assertInstanceOf(ToolbarMenuBuilder::class, $this->container->get($toolbarMenuBuilderName));
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    /**
     * @param string|null $configuration
     */
    private function compileContainer($configuration = null)
    {
        if ($configuration !== null) {
            $this->loadConfiguration($this->container, $configuration);
        }

        $this->container->compile();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createMenuFactoryMock()
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
