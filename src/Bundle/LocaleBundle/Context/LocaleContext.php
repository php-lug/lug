<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Context;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Locale\Context\LocaleContextInterface;
use Lug\Component\Locale\Negotiator\LocaleNegotiatorInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Negotiation\AcceptLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleContext implements LocaleContextInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var LocaleNegotiatorInterface
     */
    private $localeNegotiator;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param RequestStack               $requestStack
     * @param LocaleProviderInterface    $localeProvider
     * @param LocaleNegotiatorInterface  $localeNegotiator
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        LocaleNegotiatorInterface $localeNegotiator,
        ParameterResolverInterface $parameterResolver
    ) {
        $this->requestStack = $requestStack;
        $this->localeProvider = $localeProvider;
        $this->localeNegotiator = $localeNegotiator;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        if (($request = $this->requestStack->getMasterRequest()) === null) {
            return [$this->localeProvider->getDefaultLocale()->getCode()];
        }

        $locales = [];

        if ($this->parameterResolver->resolveApi()) {
            $locales = $this->parseAcceptLanguage($request);
        }

        if (empty($locales)) {
            $locales = [$request->getLocale()];
        }

        return $locales;
    }

    /**
     * @param Request $request
     *
     * @return string[]
     */
    private function parseAcceptLanguage(Request $request)
    {
        $locales = [];
        $acceptLanguage = trim($request->headers->get('Accept-Language'));

        if (!empty($acceptLanguage)) {
            $locales = array_map(function (AcceptLanguage $acceptLanguage) {
                return $acceptLanguage->getType();
            }, $this->localeNegotiator->parse($acceptLanguage));
        }

        return $locales;
    }
}
