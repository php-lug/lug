<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Context;

use Lug\Bundle\GridBundle\Exception\LocaleNotFoundException;
use Lug\Component\Grid\Context\LocaleContextInterface;
use Lug\Component\Locale\Context\LocaleContextInterface as BaseLocaleContextInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleContext implements LocaleContextInterface
{
    /**
     * @var BaseLocaleContextInterface
     */
    private $localeContext;

    /**
     * @param BaseLocaleContextInterface $localeContext
     */
    public function __construct(BaseLocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        $locales = $this->localeContext->getLocales();

        if (($locale = reset($locales)) === false) {
            throw new LocaleNotFoundException();
        }

        return $locale;
    }
}
