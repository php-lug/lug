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

use Lug\Bundle\RegistryBundle\DependencyInjection\Compiler\ConvertServiceRegistryPass;
use Lug\Bundle\RegistryBundle\Model\LazyServiceRegistry;
use Lug\Component\Registry\Model\ServiceRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConvertServiceRegistryPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConvertServiceRegistryPass
     */
    private $compiler;

    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = $this->createContainerMock();
        $this->compiler = new ConvertServiceRegistryPass();
    }

    public function testProcess()
    {
        $this->container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo($tag = 'lug.registry'))
            ->will($this->returnValue([$service = 'my.registry' => []]));

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($service))
            ->will($this->returnValue($definition = $this->createDefinitionMock()));

        $definition
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue(ServiceRegistry::class));

        $definition
            ->expects($this->once())
            ->method('clearTag')
            ->with($this->identicalTo($tag))
            ->will($this->returnSelf());

        $types = [
            ['type1', new Reference('my.id1')],
            ['type2', new Reference('my.id2')],
        ];

        $map = [];
        foreach ($types as $type) {
            $map[] = ['offsetSet', $type];
        }

        $definition
            ->expects($this->once())
            ->method('getMethodCalls')
            ->will($this->returnValue($map));

        $definition
            ->expects($this->exactly(3))
            ->method('hasMethodCall')
            ->with($this->identicalTo('offsetSet'))
            ->willReturnOnConsecutiveCalls(true, true, false);

        $definition
            ->expects($this->exactly(2))
            ->method('removeMethodCall')
            ->with('offsetSet');

        $this->container
            ->expects($this->exactly(2))
            ->method('setDefinition')
            ->withConsecutive(
                [$service.'.internal', $definition],
                [$service, $this->callback(function ($definition) use ($service, $types) {
                    $result = $definition instanceof Definition
                        && $definition->getClass() === LazyServiceRegistry::class
                        && count($definition->getArguments()) === 2
                        && $definition->getArgument(0) instanceof Reference
                        && (string) $definition->getArgument(0) === 'service_container'
                        && $definition->getArgument(1) instanceof Reference
                        && (string) $definition->getArgument(1) === $service.'.internal';

                    $result = $result && count($methodCalls = $definition->getMethodCalls()) === count($types);

                    foreach ($types as $type => $value) {
                        $value[1] = (string) $value[1];
                        $result = $result && isset($methodCalls[$type]) && $methodCalls[$type] === ['setLazy', $value];
                    }

                    return $result;
                })]
            );

        $this->compiler->process($this->container);
    }

    public function testProcessWithLazyRegistry()
    {
        $this->container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->identicalTo($tag = 'lug.registry'))
            ->will($this->returnValue([$service = 'my.registry' => []]));

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->identicalTo($service))
            ->will($this->returnValue($definition = $this->createDefinitionMock()));

        $definition
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue(LazyServiceRegistry::class));

        $definition
            ->expects($this->never())
            ->method('clearTag');

        $definition
            ->expects($this->never())
            ->method('removeMethodCall')
            ->with('offsetSet');

        $this->container
            ->expects($this->never())
            ->method('setDefinition');

        $this->compiler->process($this->container);
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerMock()
    {
        return $this->getMock(ContainerBuilder::class);
    }

    /**
     * @return Definition|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createDefinitionMock()
    {
        return $this->getMock(Definition::class);
    }
}
