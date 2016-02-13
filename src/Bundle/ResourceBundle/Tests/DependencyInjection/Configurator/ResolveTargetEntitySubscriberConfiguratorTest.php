<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\DependencyInjection\Configurator;

use Lug\Bundle\ResourceBundle\DependencyInjection\Configurator\ResolveTargetSubscriberConfigurator;
use Lug\Bundle\ResourceBundle\EventSubscriber\ResolveTargetSubscriberInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetEntitySubscriberConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResolveTargetSubscriberConfigurator
     */
    private $configurator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $serviceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->serviceRegistry = $this->createServiceRegistryMock();
        $this->configurator = new ResolveTargetSubscriberConfigurator($this->serviceRegistry);
    }

    public function testConfigure()
    {
        $this->serviceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getInterfaces')
            ->will($this->returnValue([$interface = 'interface']));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model = 'model'));

        $subscriber = $this->createResolveTargetSubscriberMock();
        $subscriber
            ->expects($this->once())
            ->method('addResolveTarget')
            ->with(
                $this->identicalTo($interface),
                $this->identicalTo($model)
            );

        $this->configurator->configure($subscriber);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResolveTargetSubscriberInterface
     */
    private function createResolveTargetSubscriberMock()
    {
        return $this->getMock(ResolveTargetSubscriberInterface::class);
    }
}
