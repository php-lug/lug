<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface as BaseRepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var ObjectRepository[]
     */
    private $cache = [];

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $metadata = $entityManager->getClassMetadata($entityName);
        $hash = $metadata->getName().spl_object_hash($entityManager);

        if (isset($this->cache[$hash])) {
            return $this->cache[$hash];
        }

        return $this->cache[$hash] = $this->createRepository($entityManager, $entityName);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityName
     *
     * @return ObjectRepository
     */
    protected function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $metadata = $entityManager->getClassMetadata($entityName);
        $repository = $metadata->customRepositoryClassName;

        if ($repository === null) {
            $repository = $entityManager->getConfiguration()->getDefaultRepositoryClassName();
        }

        return $this->createResourceRepository(
            $repository,
            $entityManager,
            $metadata,
            $this->resolveResource($metadata->getName())
        );
    }

    /**
     * @param string                 $class
     * @param EntityManagerInterface $entityManager
     * @param ClassMetadata          $metadata
     * @param ResourceInterface|null $resource
     *
     * @return ObjectRepository
     */
    protected function createResourceRepository(
        $class,
        EntityManagerInterface $entityManager,
        ClassMetadata $metadata,
        ResourceInterface $resource = null
    ) {
        if ($resource !== null && is_a($class, BaseRepositoryInterface::class, true)) {
            return new $class($entityManager, $metadata, $resource);
        }

        return new $class($entityManager, $metadata);
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
