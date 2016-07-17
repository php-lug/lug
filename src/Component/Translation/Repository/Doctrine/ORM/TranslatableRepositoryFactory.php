<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\Doctrine\ORM\RepositoryFactory;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Repository\TranslatableRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableRepositoryFactory extends RepositoryFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container->get('lug.resource.registry'));

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceRepository(
        $class,
        EntityManagerInterface $entityManager,
        ClassMetadata $metadata,
        ResourceInterface $resource = null
    ) {
        if ($resource !== null && is_a($class, TranslatableRepositoryInterface::class, true)) {
            return new $class($entityManager, $metadata, $resource, $this->getLocaleContext());
        }

        return parent::createResourceRepository($class, $entityManager, $metadata, $resource);
    }

    /**
     * @return LocaleContextInterface
     */
    private function getLocaleContext()
    {
        return $this->container->get('lug.translation.context.locale');
    }
}
