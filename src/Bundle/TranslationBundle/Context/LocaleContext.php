<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Context;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Locale\Context\LocaleContextInterface as BaseLocaleContextInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;

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
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param BaseLocaleContextInterface $localeContext
     * @param LocaleProviderInterface    $localeProvider
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(
        BaseLocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        ParameterResolverInterface $parameterResolver
    ) {
        $this->localeContext = $localeContext;
        $this->localeProvider = $localeProvider;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        return $this->localeContext->getLocales();
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocale()
    {
        if (!$this->parameterResolver->resolveApi()) {
            return $this->localeProvider->getDefaultLocale()->getCode();
        }
    }
}
