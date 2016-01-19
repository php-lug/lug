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

use Lug\Component\Resource\Exception\DomainException;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDomainManager implements DomainManagerInterface
{
    const STATE_PRE = 'pre';
    const STATE_POST = 'post';
    const STATE_ERROR = 'error';

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ResourceInterface        $resource
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ResourceInterface $resource, EventDispatcherInterface $eventDispatcher)
    {
        $this->resource = $resource;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function find($action, $repositoryMethod, array $criteria, array $sorting = [])
    {
        $event = new DomainEvent($this->resource, null, $action = 'find.'.$action);

        try {
            $this->dispatchEvent($event, $action, self::STATE_PRE);
            $result = $this->doFind($action, $repositoryMethod, $criteria, $sorting);
        } catch (\Exception $e) {
            $this->dispatchEvent($event, $action, self::STATE_ERROR, $e);

            return;
        }

        $this->dispatchEvent(new DomainEvent($this->resource, $result, $action), $action, self::STATE_POST);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function create($object, $flush = true)
    {
        $event = new DomainEvent($this->resource, $object, $action = 'create');

        try {
            $this->dispatchEvent($event, $action, self::STATE_PRE);
            $this->doCreate($object, $flush);
        } catch (\Exception $e) {
            $this->dispatchEvent($event, $action, self::STATE_ERROR, $e);

            return;
        }

        $this->dispatchEvent($event, $action, self::STATE_POST);
    }

    /**
     * {@inheritdoc}
     */
    public function update($object, $flush = true)
    {
        $event = new DomainEvent($this->resource, $object, $action = 'update');

        try {
            $this->dispatchEvent($event, $action, self::STATE_PRE);
            $this->doUpdate($object, $flush);
        } catch (\Exception $e) {
            $this->dispatchEvent($event, $action, self::STATE_ERROR, $e);

            return;
        }

        $this->dispatchEvent($event, $action, self::STATE_POST);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $flush = true)
    {
        $event = new DomainEvent($this->resource, $object, $action = 'delete');

        try {
            $this->dispatchEvent($event, $action, self::STATE_PRE);
            $this->doDelete($object, $flush);
        } catch (\Exception $e) {
            $this->dispatchEvent($event, $action, self::STATE_ERROR, $e);

            return;
        }

        $this->dispatchEvent($event, $action, self::STATE_POST);
    }

    /**
     * {@inheritdoc}
     */
    public function flush($object = null)
    {
        $event = new DomainEvent($this->resource, $object, $action = 'flush');

        try {
            $this->dispatchEvent($event, $action, self::STATE_PRE);
            $this->doFlush();
        } catch (\Exception $e) {
            $this->dispatchEvent($event, $action, self::STATE_ERROR, $e);

            return;
        }

        $this->dispatchEvent($event, $action, self::STATE_POST);
    }

    /**
     * @param string  $action
     * @param string  $repositoryMethod
     * @param mixed[] $criteria
     * @param mixed[] $sorting
     *
     * @return object|null
     */
    abstract protected function doFind($action, $repositoryMethod, array $criteria, array $sorting);

    /**
     * @param object $object
     * @param bool   $flush
     */
    abstract protected function doCreate($object, $flush = true);

    /**
     * @param object $object
     * @param bool   $flush
     */
    abstract protected function doUpdate($object, $flush = true);

    /**
     * @param object $object
     * @param bool   $flush
     */
    abstract protected function doDelete($object, $flush = true);

    abstract protected function doFlush();

    /**
     * @param DomainEvent     $event
     * @param string          $action
     * @param string          $state
     * @param \Exception|null $exception
     */
    private function dispatchEvent(DomainEvent $event, $action, $state, \Exception $exception = null)
    {
        if ($state === self::STATE_ERROR) {
            $event->setStopped(true);
        }

        $this->eventDispatcher->dispatch('lug.'.$this->resource->getName().'.'.$state.'_'.$action, $event);

        if ($event->isStopped()) {
            throw new DomainException($event->getStatusCode(), $event->getMessage(), 0, $exception);
        }
    }
}
