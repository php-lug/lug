<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Repository\Doctrine\MongoDB;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Repository\DefaultRepositoryFactory;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RepositoryFactory extends DefaultRepositoryFactory
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
     * {@inheritdoc}
     */
    protected function createRepository(DocumentManager $documentManager, $documentName)
    {
        $metadata = $documentManager->getClassMetadata($documentName);
        $repository = $metadata->customRepositoryClassName;

        if ($repository === null) {
            $repository = $documentManager->getConfiguration()->getDefaultRepositoryClassName();
        }

        return $this->createResourceRepository(
            $repository,
            $documentManager,
            $metadata,
            $this->resolveResource($metadata->getName())
        );
    }

    /**
     * @param string                 $class
     * @param DocumentManager        $documentManager
     * @param ClassMetadata          $metadata
     * @param ResourceInterface|null $resource
     *
     * @return ObjectRepository
     */
    protected function createResourceRepository(
        $class,
        DocumentManager $documentManager,
        ClassMetadata $metadata,
        ResourceInterface $resource = null
    ) {
        if ($resource !== null && is_a($class, RepositoryInterface::class, true)) {
            return new $class($documentManager, $documentManager->getUnitOfWork(), $metadata, $resource);
        }

        return parent::createRepository($documentManager, $metadata->getName());
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
