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

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterManagerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterManagerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterManagerPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterManagerPass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->compiler);
    }

    public function testProcess()
    {
        $manager = 'my.manager';
        $resource = 'my.resource';

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.manager'))
            ->will($this->returnValue([$manager => [['resource' => $resource]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo('lug.resource.registry.manager'))
            ->will($this->returnValue($registry = $this->createDefinitionMock()));

        $registry
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                $this->identicalTo('offsetSet'),
                $this->callback(function (array $args) use ($manager, $resource) {
                    return isset($args[0])
                        && isset($args[1])
                        && $args[0] === $resource
                        && $args[1] instanceof Reference
                        && (string) $args[1] === $manager;
                })
            );

        $this->compiler->process($container);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\TagAttributeNotFoundException
     * @expectedExceptionMessage The attribute "resource" could not be found for the tag "lug.manager" on the "my.manager" service.
     */
    public function testProcessWithMissingResourceAttribute()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.manager'))
            ->will($this->returnValue([$manager = 'my.manager' => [[]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo('lug.resource.registry.manager'))
            ->will($this->returnValue($registry = $this->createDefinitionMock()));

        $this->compiler->process($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->getMock(ContainerBuilder::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Definition
     */
    private function createDefinitionMock()
    {
        return $this->getMock(Definition::class);
    }
}
