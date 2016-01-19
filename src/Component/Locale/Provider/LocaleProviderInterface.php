<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Provider;

use Lug\Component\Locale\Model\LocaleInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface LocaleProviderInterface
{
    /**
     * @return LocaleInterface
     */
    public function getDefaultLocale();

    /**
     * @return LocaleInterface[]
     */
    public function getLocales();

    /**
     * @return LocaleInterface[]
     */
    public function getRequiredLocales();
}
