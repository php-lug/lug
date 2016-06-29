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
use Lug\Component\Registry\Model\RegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetSubscriberConfigurator
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * @param ResolveTargetSubscriberInterface $resolveTargetObjectSubscriber
     */
    public function configure(ResolveTargetSubscriberInterface $resolveTargetObjectSubscriber)
    {
        foreach ($this->resourceRegistry as $resource) {
            if ($resource->getDriver() === null) {
                continue;
            }

            foreach ($resource->getInterfaces() as $interface) {
                $resolveTargetObjectSubscriber->addResolveTarget($interface, $resource->getModel());
            }
        }
    }
}
