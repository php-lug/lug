<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle;

use Lug\Bundle\ResourceBundle\DependencyInjection\Extension\ResourceExtension;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractResourceBundle extends Bundle implements ResourceBundleInterface
{
    /**
     * @var string
     */
    private $driver;

    /**
     * @var ResourceInterface[]
     */
    private $resources;

    /**
     * @param string $driver
     */
    public function __construct($driver = ResourceInterface::DRIVER_DOCTRINE_ORM)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        if ($this->resources === null) {
            $this->resources = $this->createResources($this->driver);
        }

        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return Container::underscore(preg_replace('/Bundle$/', '', $this->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function createContainerExtension()
    {
        if (!class_exists($class = $this->getContainerExtensionClass())) {
            $class = ResourceExtension::class;
        }

        return new $class($this);
    }

    /**
     * @param string $driver
     *
     * @return ResourceInterface[]
     */
    abstract protected function createResources($driver);
}
