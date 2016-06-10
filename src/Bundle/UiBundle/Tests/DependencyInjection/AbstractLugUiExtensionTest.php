<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\Tests\DependencyInjection;

use Knp\Menu\FactoryInterface;
use Lug\Bundle\UiBundle\DependencyInjection\LugUiExtension;
use Lug\Bundle\UiBundle\Form\Extension\IconButtonExtension;
use Lug\Bundle\UiBundle\Form\Extension\IconFormExtension;
use Lug\Bundle\UiBundle\Menu\MenuBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugUiExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugUiExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var FactoryInterface
     */
    private $menuFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->menuFactory = $this->createMenuFactoryMock();
        $this->eventDispatcher = $this->createEventDispatcherMock();
        $this->extension = new LugUiExtension();

        $this->container = new ContainerBuilder();
        $this->container->set('knp_menu.factory', $this->menuFactory);
        $this->container->set('event_dispatcher', $this->eventDispatcher);
        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testForm()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($iconFormName = 'lug.ui.form.extension.icon.form'));
        $this->assertTrue($this->container->hasDefinition($iconButtonName = 'lug.ui.form.extension.icon.button'));

        $iconFormDefinition = $this->container->getDefinition($iconFormName);
        $iconButtonDefinition = $this->container->getDefinition($iconButtonName);

        $this->assertTrue($iconFormDefinition->hasTag($formTypeExtensionTag = 'form.type_extension'));
        $this->assertSame([[
            'extended_type' => FormType::class,
            'extended-type' => FormType::class,
        ]], $iconFormDefinition->getTag($formTypeExtensionTag));

        $this->assertTrue($iconButtonDefinition->hasTag($formTypeExtensionTag));
        $this->assertSame([[
            'extended_type' => ButtonType::class,
            'extended-type' => ButtonType::class,
        ]], $iconButtonDefinition->getTag($formTypeExtensionTag));

        $this->assertInstanceOf(IconFormExtension::class, $this->container->get($iconFormName));
        $this->assertInstanceOf(IconButtonExtension::class, $this->container->get($iconButtonName));
    }

    public function testMenu()
    {
        $definition = new DefinitionDecorator('lug.ui.menu.builder');
        $definition->setClass($class = $this->createMenuBuilderClassMock());
        $this->container->setDefinition($menuName = 'lug.ui.menu.test', $definition);

        $this->compileContainer();

        $this->assertInstanceOf(
            ContainerAwareEventDispatcher::class,
            $this->container->get('lug.ui.menu.event_dispatcher')
        );

        $this->assertInstanceOf($class, $this->container->get($menuName));
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

    /**
     * @return string
     */
    private function createMenuBuilderClassMock()
    {
        return $this->getMockClass(MenuBuilderInterface::class);
    }
}
