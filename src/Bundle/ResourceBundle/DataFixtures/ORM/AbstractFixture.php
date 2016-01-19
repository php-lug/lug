<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture as DoctrineFixture;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractFixture extends DoctrineFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string|null $name
     *
     * @return ResourceInterface
     */
    protected function getResource($name = null)
    {
        return $this->container->get('lug.resource.registry')[$name ?: $this->getResourceName()];
    }

    /**
     * @param string|null $name
     *
     * @return FactoryInterface
     */
    protected function getFactory($name = null)
    {
        return $this->container->get('lug.resource.registry.factory')[$name ?: $this->getResourceName()];
    }

    /**
     * @return string
     */
    abstract protected function getResourceName();
}
