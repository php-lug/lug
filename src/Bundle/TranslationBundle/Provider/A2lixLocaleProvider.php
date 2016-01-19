<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Provider;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface as BaseLocaleProviderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class A2lixLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var BaseLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param BaseLocaleProviderInterface $localeProvider
     */
    public function __construct(BaseLocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->localeProvider->getDefaultLocale()->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        return array_map($this->getCallback(), $this->localeProvider->getLocales());
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredLocales()
    {
        return array_map($this->getCallback(), $this->localeProvider->getRequiredLocales());
    }

    /**
     * @return callable
     */
    private function getCallback()
    {
        return function (LocaleInterface $locale) {
            return $locale->getCode();
        };
    }
}
