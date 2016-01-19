<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Behat\Dictionary;

use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait ContainerDictionary
{
    use KernelDictionary;

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasService($id)
    {
        return $this->getContainer()->has($id);
    }

    /**
     * @param string $id
     *
     * @return object
     */
    public function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        return $this->getContainer()->hasParameter($name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->getContainer()->getParameter($name);
    }
}
