<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\Tests\DependencyInjection\Compiler;

use Knp\Menu\ItemInterface;
use Lug\Bundle\UiBundle\DependencyInjection\Compiler\RegisterMenuPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterMenuPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterMenuPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterMenuPass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->compiler);
    }

    public function testProcess()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.menu.builder'))
            ->will($this->returnValue([$service = 'my.service' => [['alias' => $alias = 'my.alias']]]));

        $container
            ->expects($this->once())
            ->method('setDefinition')
            ->with(
                $this->identicalTo($service.'.menu'),
                $this->callback(function (Definition $definition) use ($service, $alias) {
                    return $definition->getClass() === ItemInterface::class
                        && $definition->getTag('knp_menu.menu') === [['alias' => $alias]]
                        && is_array($factory = $definition->getFactory())
                        && isset($factory[0])
                        && $factory[0] instanceof Reference
                        && (string) $factory[0] === $service
                        && isset($factory[1])
                        && $factory[1] === 'create';
                })
            );

        $this->compiler->process($container);
    }

    /**
     * @expectedException \Lug\Bundle\UiBundle\Exception\TagAttributeNotFoundException
     * @expectedExceptionMessage The attribute "alias" could not be found for the tag "lug.menu.builder" on the "my.service" service.
     */
    public function testProcessWithoutAlias()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.menu.builder'))
            ->will($this->returnValue([$service = 'my.service' => [[]]]));

        $this->compiler->process($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->getMock(ContainerBuilder::class);
    }
}
