<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Registry\Model;

use Lug\Component\Registry\Exception\InvalidServiceException;
use Lug\Component\Registry\Exception\ServiceAlreadyExistsException;
use Lug\Component\Registry\Exception\ServiceNotFoundException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * @var string
     */
    private $interface;

    /**
     * @var object[]
     */
    private $services = [];

    /**
     * @param string   $interface
     * @param object[] $services
     */
    public function __construct($interface, array $services = [])
    {
        $this->interface = $interface;

        foreach ($services as $type => $service) {
            $this->offsetSet($type, $service);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($type)
    {
        return array_key_exists($type, $this->services);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($type)
    {
        if (!$this->offsetExists($type)) {
            throw new ServiceNotFoundException(sprintf('The service "%s" could not be found.', $type));
        }

        return $this->services[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($type, $service)
    {
        if ($this->offsetExists($type)) {
            throw new ServiceAlreadyExistsException(sprintf('The service "%s" already exists.', $type));
        }

        if (!$service instanceof $this->interface) {
            throw new InvalidServiceException(sprintf(
                'The service for the registry "%s" must be an instance of "%s", got "%s".',
                get_class($this),
                $this->interface,
                is_object($service) ? get_class($service) : gettype($service)
            ));
        }

        $this->services[$type] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($type)
    {
        if (!$this->offsetExists($type)) {
            throw new ServiceNotFoundException(sprintf('The service "%s" could not be found.', $type));
        }

        unset($this->services[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->services);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->services);
    }
}
