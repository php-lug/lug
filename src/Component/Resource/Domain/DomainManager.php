<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Domain;

use Doctrine\Common\Persistence\ObjectManager;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainManager extends AbstractDomainManager
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param ResourceInterface        $resource
     * @param EventDispatcherInterface $eventDispatcher
     * @param ObjectManager            $manager
     * @param RepositoryInterface      $repository
     */
    public function __construct(
        ResourceInterface $resource,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $manager,
        RepositoryInterface $repository
    ) {
        parent::__construct($resource, $eventDispatcher);

        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFind($action, $repositoryMethod, array $criteria, array $sorting)
    {
        return $this->repository->$repositoryMethod($criteria, $sorting);
    }

    /**
     * {@inheritdoc}
     */
    protected function doCreate($object, $flush = true)
    {
        $this->manager->persist($object);

        if ($flush) {
            $this->flush($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doUpdate($object, $flush = true)
    {
        if (!$this->manager->contains($object)) {
            $this->manager->persist($object);
        }

        if ($flush) {
            $this->flush($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($object, $flush = true)
    {
        $this->manager->remove($object);

        if ($flush) {
            $this->flush($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        $this->manager->flush();
    }
}
