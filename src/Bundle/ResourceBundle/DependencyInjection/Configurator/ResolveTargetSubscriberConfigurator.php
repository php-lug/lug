<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DependencyInjection\Configurator;

use Lug\Bundle\ResourceBundle\EventSubscriber\ResolveTargetSubscriberInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetSubscriberConfigurator
{
    /**
     * @var ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param ServiceRegistryInterface $resourceRegistry
     */
    public function __construct(ServiceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * @param ResolveTargetSubscriberInterface $resolveTargetObjectSubscriber
     */
    public function configure(ResolveTargetSubscriberInterface $resolveTargetObjectSubscriber)
    {
        foreach ($this->resourceRegistry as $resource) {
            foreach ($resource->getInterfaces() as $interface) {
                $resolveTargetObjectSubscriber->addResolveTarget($interface, $resource->getModel());
            }
        }
    }
}
