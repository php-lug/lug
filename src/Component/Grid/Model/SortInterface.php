<?php


/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Model;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface SortInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function hasOptions();

    /**
     * @return mixed[]
     */
    public function getOptions();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOption($name);
}
