<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Context;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface LocaleContextInterface
{
    /**
     * @return string[]
     */
    public function getLocales();
}
