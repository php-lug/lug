<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Model;

use Lug\Component\Grid\Model\FilterInterface as BaseFilterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FilterInterface extends BaseFilterInterface
{
    /**
     * @return string
     */
    public function getForm();

    /**
     * @return bool
     */
    public function hasFormOptions();

    /**
     * @return mixed[]
     */
    public function getFormOptions();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFormOption($name);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getFormOption($name);
}
