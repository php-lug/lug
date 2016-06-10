<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\Context;

use Lug\Bundle\LocaleBundle\Context\LocaleContext;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Negotiator\LocaleNegotiatorInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Negotiation\AcceptLanguage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleContext
     */
    private $localeContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleNegotiatorInterface
     */
    private $localeNegotiator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->requestStack = $this->createRequestStackMock();
        $this->localeProvider = $this->createLocaleProviderMock();
        $this->localeNegotiator = $this->createLocaleNegotiatorMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->localeContext = new LocaleContext(
            $this->requestStack,
            $this->localeProvider,
            $this->localeNegotiator,
            $this->parameterResolver
        );
    }

    public function testLocalesWithRequest()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($locale = 'en'));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        $this->assertSame([$locale], $this->localeContext->getLocales());
    }

    public function testLocalesWithoutRequest()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($locale = $this->createLocaleMock()));

        $locale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($code = 'en'));

        $this->assertSame([$code], $this->localeContext->getLocales());
    }

    public function testLocalesWithApiRequestAndAcceptLanguageHeader()
    {
        $request = $this->createRequestMock();
        $request->headers
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('Accept-Language'))
            ->will($this->returnValue($acceptLanguage = 'accept-language'));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        $this->localeNegotiator
            ->expects($this->once())
            ->method('parse')
            ->with($this->identicalTo('accept-language'))
            ->will($this->returnValue([new AcceptLanguage($locale = 'fr')]));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->assertSame([$locale], $this->localeContext->getLocales());
    }

    public function testLocalesWithApiRequestWithoutAcceptLanguageHeader()
    {
        $request = $this->createRequestMock();
        $request->headers
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('Accept-Language'))
            ->will($this->returnValue(null));

        $request
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($locale = 'en'));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->assertSame([$locale], $this->localeContext->getLocales());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->createMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->createMock(LocaleProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleNegotiatorInterface
     */
    private function createLocaleNegotiatorMock()
    {
        return $this->createMock(LocaleNegotiatorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createRequestMock()
    {
        $request = $this->createMock(Request::class);
        $request->headers = $this->createParameterBagMock();

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterBagInterface
     */
    private function createParameterBagMock()
    {
        return $this->createMock(ParameterBagInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->createMock(LocaleInterface::class);
    }
}
