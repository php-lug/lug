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

use Lug\Component\Registry\Model\RegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface LazyRegistryInterface extends RegistryInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasLazy($type);

    /**
     * @param string $type
     *
     * @return string
     */
    public function getLazy($type);

    /**
     * @param string $type
     * @param string $service
     */
    public function setLazy($type, $service);

    /**
     * @param string $type
     */
    public function removeLazy($type);
}
