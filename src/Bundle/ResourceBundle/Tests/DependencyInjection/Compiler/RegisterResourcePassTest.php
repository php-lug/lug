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

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcePass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterResourcePassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterResourcePass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterResourcePass();
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
            ->with($this->identicalTo('lug.resource'))
            ->will($this->returnValue([$resource = 'my.resource' => [[]]]));

        $container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with($this->identicalTo('lug.resource.registry'))
            ->will($this->returnValue($registry = $this->createDefinitionMock()));

        $container
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with($this->identicalTo($resource))
            ->will($this->returnValue($resourceDefinition = $this->createDefinitionMock()));

        $resourceDefinition
            ->expects($this->once())
            ->method('getArgument')
            ->with($this->identicalTo(0))
            ->will($this->returnValue($name = 'resource'));

        $registry
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                $this->identicalTo('offsetSet'),
                $this->callback(function (array $args) use ($name, $resource) {
                    return isset($args[0])
                        && isset($args[1])
                        && $args[0] === $name
                        && $args[1] instanceof Reference
                        && (string) $args[1] === $resource;
                })
            );

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
