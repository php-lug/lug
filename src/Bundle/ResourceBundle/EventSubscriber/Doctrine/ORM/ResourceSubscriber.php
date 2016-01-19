<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceSubscriber implements EventSubscriber
{
    /**
     * @var ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->resourceRegistry = $registry;
    }

    /**
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $classMetadata = $event->getClassMetadata();

        if (($resource = $this->resolveResource($classMetadata->getName())) !== null) {
            $classMetadata->isMappedSuperclass = false;
            $classMetadata->setCustomRepositoryClass($resource->getRepository());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }

    /**
     * @param string $class
     *
     * @return ResourceInterface|null
     */
    private function resolveResource($class)
    {
        foreach ($this->resourceRegistry as $resource) {
            if ($resource->getModel() === $class) {
                return $resource;
            }
        }
    }
}
