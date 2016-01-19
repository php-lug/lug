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

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterDomainManagerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterDomainManagerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterDomainManagerPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterDomainManagerPass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->compiler);
    }

    public function testProcess()
    {
        $domainManager = 'my.domain_manager';
        $resource = 'my.resource';

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.domain_manager'))
            ->will($this->returnValue([$domainManager => [['resource' => $resource]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo('lug.resource.registry.domain_manager'))
            ->will($this->returnValue($registry = $this->createDefinitionMock()));

        $registry
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                $this->identicalTo('offsetSet'),
                $this->callback(function (array $args) use ($domainManager, $resource) {
                    return isset($args[0])
                        && isset($args[1])
                        && $args[0] === $resource
                        && $args[1] instanceof Reference
                        && (string) $args[1] === $domainManager;
                })
            );

        $this->compiler->process($container);
    }

    /**
     * @expectedException \Lug\Bundle\RegistryBundle\Exception\TagAttributeNotFoundException
     * @expectedExceptionMessage The attribute "resource" could not be found for the tag "lug.domain_manager" on the "my.domain_manager" service.
     */
    public function testProcessWithMissingResourceAttribute()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo('lug.domain_manager'))
            ->will($this->returnValue([$domainManager = 'my.domain_manager' => [[]]]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo('lug.resource.registry.domain_manager'))
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
