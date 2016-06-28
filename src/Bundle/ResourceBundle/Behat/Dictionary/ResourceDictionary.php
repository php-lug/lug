<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Behat\Dictionary;

use Doctrine\Common\Persistence\ObjectManager;
use Lug\Component\Behat\Dictionary\ContainerDictionary;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait ResourceDictionary
{
    use ContainerDictionary;

    /**
     * @param string $name
     *
     * @return FactoryInterface
     */
    public function getFactory($name)
    {
        return $this->getService('lug.resource.registry.factory')[$name];
    }

    /**
     * @param string $name
     *
     * @return ObjectManager
     */
    public function getManager($name)
    {
        return $this->getService('lug.resource.registry.manager')[$name];
    }

    /**
     * @param string $name
     *
     * @return RepositoryInterface
     */
    public function getRepository($name)
    {
        return $this->getService('lug.resource.registry.repository')[$name];
    }
}
