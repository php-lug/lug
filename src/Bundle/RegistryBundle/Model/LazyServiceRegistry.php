<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\RegistryBundle\Model;

use Lug\Bundle\RegistryBundle\Exception\LazyServiceAlreadyExistsException;
use Lug\Bundle\RegistryBundle\Exception\LazyServiceNotFoundException;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LazyServiceRegistry implements LazyServiceRegistryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * @var string[]
     */
    private $services = [];

    /**
     * @param ContainerInterface       $container
     * @param ServiceRegistryInterface $registry
     * @param string[]                 $services
     */
    public function __construct(ContainerInterface $container, ServiceRegistryInterface $registry, array $services = [])
    {
        $this->container = $container;
        $this->registry = $registry;

        foreach ($services as $type => $service) {
            $this->setLazy($type, $service);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasLazy($type)
    {
        return array_key_exists($type, $this->services) && $this->container->has($this->services[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLazy($type)
    {
        if (!$this->hasLazy($type)) {
            throw new LazyServiceNotFoundException(sprintf('The lazy service "%s" could not be found.', $type));
        }

        return $this->services[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function setLazy($type, $service)
    {
        if ($this->hasLazy($type)) {
            throw new LazyServiceAlreadyExistsException(sprintf('The lazy service "%s" already exists.', $type));
        }

        $this->services[$type] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function removeLazy($type)
    {
        if (!$this->hasLazy($type)) {
            throw new LazyServiceNotFoundException(sprintf('The lazy service "%s" could not be found.', $type));
        }

        unset($this->services[$type]);

        if (isset($this->registry[$type])) {
            unset($this->registry[$type]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->hasLazy($offset) || isset($this->registry[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $this->lazyLoad($offset);

        return $this->registry[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($this->hasLazy($offset)) {
            $this->lazyLoad($offset);
        }

        $this->registry[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ($this->hasLazy($offset)) {
            $this->lazyLoad($offset);
        }

        unset($this->registry[$offset]);

        if ($this->hasLazy($offset)) {
            $this->removeLazy($offset);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->load();

        return count($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->load();

        return $this->registry->getIterator();
    }

    private function load()
    {
        foreach (array_keys($this->services) as $type) {
            $this->lazyLoad($type);
        }
    }

    /**
     * @param string $type
     */
    private function lazyLoad($type)
    {
        if (!isset($this->registry[$type])) {
            $this->registry[$type] = $this->container->get($this->getLazy($type));
        }
    }
}
