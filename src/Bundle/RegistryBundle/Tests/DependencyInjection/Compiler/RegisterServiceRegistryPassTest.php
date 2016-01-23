<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\RegistryBundle\Tests\DependencyInjection\Compiler;

use Lug\Bundle\RegistryBundle\DependencyInjection\Compiler\AbstractRegisterServiceRegistryPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterServiceRegistryPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractRegisterServiceRegistryPass
     */
    private $compiler;

    /**
     * @var string
     */
    private $registry;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $attribute;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registry = 'my.registry';
        $this->tag = 'my.tag';
        $this->attribute = 'my.alias';

        $this->compiler = $this->getMockBuilder(AbstractRegisterServiceRegistryPass::class)
            ->setConstructorArgs([$this->registry, $this->tag, $this->attribute])
            ->getMockForAbstractClass();
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
            ->with($this->identicalTo($this->tag))
            ->will($this->returnValue([$service = 'my.service' => [[$this->attribute => $name = 'my.name']]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($this->registry))
            ->will($this->returnValue($registry = $this->createDefinitionMock()));

        $registry
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                $this->identicalTo('offsetSet'),
                $this->callback(function (array $args) use ($service, $name) {
                    return isset($args[0])
                        && isset($args[1])
                        && $args[0] === $name
                        && $args[1] instanceof Reference
                        && (string) $args[1] === $service;
                })
            );

        $this->compiler->process($container);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\TagAttributeNotFoundException
     * @expectedExceptionMessage The attribute "my.alias" could not be found for the tag "my.tag" on the "my.service" service.
     */
    public function testProcessWithMissingResourceAttribute()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo($this->tag))
            ->will($this->returnValue(['my.service' => [[]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($this->registry))
            ->will($this->returnValue($registry = $this->createDefinitionMock()));

        $this->compiler->process($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->getMock(ContainerBuilder::class, ['findTaggedServiceIds', 'getDefinition']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Definition
     */
    private function createDefinitionMock()
    {
        return $this->getMock(Definition::class);
    }
}
