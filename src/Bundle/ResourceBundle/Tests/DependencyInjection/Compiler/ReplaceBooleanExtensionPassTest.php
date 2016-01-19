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

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ReplaceBooleanExtensionPass;
use Lug\Bundle\ResourceBundle\Form\Extension\BooleanExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ReplaceBooleanExtensionPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReplaceBooleanExtensionPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new ReplaceBooleanExtensionPass();
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
            ->method('getDefinition')
            ->with($this->identicalTo('fos_rest.form.extension.boolean'))
            ->will($this->returnValue($definition = $this->createDefinitionMock()));

        $definition
            ->expects($this->once())
            ->method('setClass')
            ->with($this->identicalTo(BooleanExtension::class))
            ->will($this->returnSelf());

        $definition
            ->expects($this->once())
            ->method('addArgument')
            ->with($this->callback(function ($reference) {
                return $reference instanceof Reference
                    && (string) $reference === 'lug.resource.routing.parameter_resolver';
            }))
            ->will($this->returnSelf());

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
