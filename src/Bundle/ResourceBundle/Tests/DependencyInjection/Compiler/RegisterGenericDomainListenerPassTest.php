<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\DependencyInjection\Compiler;

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\AbstractRegisterGenericDomainListenerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterGenericDomainListenerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractRegisterGenericDomainListenerPass
     */
    private $compiler;

    /**
     * @var string
     */
    private $listener;

    /**
     * @var mixed[]
     */
    private $event;

    /**
     * @var string[]
     */
    private $actions;

    /**
     * @var string[]
     */
    private $prefixes;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->listener = 'my.listener';
        $this->event = ['method' => 'myMethod'];
        $this->actions = ['create', 'update', 'delete'];
        $this->prefixes = ['error', 'post'];

        $this->compiler = $this->getMockBuilder(AbstractRegisterGenericDomainListenerPass::class)
            ->setConstructorArgs([$this->listener, $this->event, $this->actions, $this->prefixes])
            ->getMockForAbstractClass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->compiler);
    }

    public function testProcess()
    {
        $controller = 'my.controller';
        $resource = 'my.resource';

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.controller'))
            ->will($this->returnValue([$controller => [['resource' => $resource]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($this->listener))
            ->will($this->returnValue($listener = $this->createDefinitionMock()));

        $index = 0;

        foreach ($this->actions as $action) {
            foreach ($this->prefixes as $prefix) {
                $listener
                    ->expects($this->at($index++))
                    ->method('addTag')
                    ->with(
                        $this->identicalTo('lug.resource.domain.event_listener'),
                        $this->identicalTo(array_merge([
                            'event' => 'lug.'.$resource.'.'.$prefix.'_'.$action,
                        ], $this->event))
                    );
            }
        }

        $this->compiler->process($container);
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\TagAttributeNotFoundException
     * @expectedExceptionFlash The attribute "resource" could not be found for the tag "lug.domain_manager" on the "my.domain_manager" service.
     */
    public function testProcessWithMissingResourceAttribute()
    {
        $controller = 'my.controller';

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.controller'))
            ->will($this->returnValue([$controller => [[]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($this->listener))
            ->will($this->returnValue($listener = $this->createDefinitionMock()));

        $this->compiler->process($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->createMock(ContainerBuilder::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Definition
     */
    private function createDefinitionMock()
    {
        return $this->createMock(Definition::class);
    }
}
