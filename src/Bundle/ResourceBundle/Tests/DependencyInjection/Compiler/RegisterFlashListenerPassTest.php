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

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterFlashListenerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterFlashListenerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterFlashListenerPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterFlashListenerPass();
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
            ->with($this->identicalTo('lug.resource.listener.flash'))
            ->will($this->returnValue($listener = $this->createDefinitionMock()));

        $index = 0;

        foreach (['create', 'update', 'delete'] as $action) {
            foreach (['error', 'post'] as $prefix) {
                $listener
                    ->expects($this->at($index++))
                    ->method('addTag')
                    ->with(
                        $this->identicalTo('lug.resource.domain.event_listener'),
                        $this->identicalTo([
                            'event'    => 'lug.'.$resource.'.'.$prefix.'_'.$action,
                            'method'   => 'addFlash',
                            'priority' => -2000,
                        ])
                    );
            }
        }

        $this->compiler->process($container);
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\TagAttributeNotFoundException
     * @expectedExceptionFlash You must provide the "resource" attribute for the "lug.controller" tag on the "my.controller" service.
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
            ->with($this->identicalTo('lug.resource.listener.flash'))
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
