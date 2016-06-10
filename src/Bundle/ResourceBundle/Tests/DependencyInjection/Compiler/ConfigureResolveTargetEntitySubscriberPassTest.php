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

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ConfigureResolveTargetEntitySubscriberPass;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB\ResolveTargetDocumentSubscriber;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM\ResolveTargetEntitySubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigureResolveTargetEntitySubscriberPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigureResolveTargetEntitySubscriberPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new ConfigureResolveTargetEntitySubscriberPass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->compiler);
    }

    public function testProcessWithDoctrineOrm()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->exactly(2))
            ->method('hasDefinition')
            ->will($this->returnValueMap([
                [$defaultResolveTargetEntity = 'doctrine.orm.listeners.resolve_target_entity', true],
                ['doctrine_mongodb.odm.listeners.resolve_target_document', false],
            ]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($defaultResolveTargetEntity))
            ->will($this->returnValue($definition = $this->createDefinitionMock()));

        $definition
            ->expects($this->once())
            ->method('setClass')
            ->with($this->identicalTo(ResolveTargetEntitySubscriber::class))
            ->will($this->returnSelf());

        $definition
            ->expects($this->once())
            ->method('setConfigurator')
            ->with($this->callback(function (array $configurator) {
                return isset($configurator[0])
                    && isset($configurator[1])
                    && $configurator[0] instanceof Reference
                    && (string) $configurator[0] === 'lug.resource.configurator.resolve_target_entity'
                    && $configurator[1] === 'configure';
            }))
            ->will($this->returnValue($resolveTargetEntity = $this->createDefinitionMock()));

        $resolveTargetEntity
            ->expects($this->once())
            ->method('hasTag')
            ->with($this->identicalTo($tag = 'doctrine.event_subscriber'))
            ->will($this->returnValue(false));

        $resolveTargetEntity
            ->expects($this->once())
            ->method('addTag')
            ->with($this->identicalTo($tag));

        $this->compiler->process($container);
    }

    public function testProcessWithDoctrineMongoDb()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->exactly(2))
            ->method('hasDefinition')
            ->will($this->returnValueMap([
                ['doctrine.orm.listeners.resolve_target_entity', false],
                [$defaultResolveTargetDocument = 'doctrine_mongodb.odm.listeners.resolve_target_document', true],
            ]));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($defaultResolveTargetDocument))
            ->will($this->returnValue($definition = $this->createDefinitionMock()));

        $definition
            ->expects($this->once())
            ->method('setClass')
            ->with($this->identicalTo(ResolveTargetDocumentSubscriber::class))
            ->will($this->returnSelf());

        $definition
            ->expects($this->once())
            ->method('setConfigurator')
            ->with($this->callback(function (array $configurator) {
                return isset($configurator[0])
                && isset($configurator[1])
                && $configurator[0] instanceof Reference
                && (string) $configurator[0] === 'lug.resource.configurator.resolve_target_entity'
                && $configurator[1] === 'configure';
            }))
            ->will($this->returnValue($resolveTargetEntity = $this->createDefinitionMock()));

        $resolveTargetEntity
            ->expects($this->once())
            ->method('hasTag')
            ->with($this->identicalTo($tag = 'doctrine_mongodb.odm.event_subscriber'))
            ->will($this->returnValue(false));

        $resolveTargetEntity
            ->expects($this->once())
            ->method('addTag')
            ->with($this->identicalTo($tag));

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
