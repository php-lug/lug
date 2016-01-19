<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Negotiator;

use Negotiation\AcceptLanguage;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface LocaleNegotiatorInterface
{
    /**
     * @param string $header
     *
     * @return AcceptLanguage[]
     */
    public function parse($header);
}
