<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceSubscriber implements EventSubscriber
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->resourceRegistry = $registry;
    }

    /**
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $event->getClassMetadata();

        if (($resource = $this->resolveResource($classMetadata->getName())) !== null) {
            foreach ($classMetadata->parentClasses as $parentClass) {
                try {
                    $parentMetadata = $event->getObjectManager()->getClassMetadata($parentClass);
                } catch (MappingException $e) {
                    continue;
                }

                /** @var ClassMetadata $parentMetadata */
                if ($parentMetadata->inheritanceType === ClassMetadata::INHERITANCE_TYPE_NONE) {
                    $parentMetadata->isMappedSuperclass = true;
                }
            }

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
