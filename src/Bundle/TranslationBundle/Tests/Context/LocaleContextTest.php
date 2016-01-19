<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\Context;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Bundle\TranslationBundle\Context\LocaleContext;
use Lug\Component\Locale\Context\LocaleContextInterface as BaseLocaleContextInterface;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|ComponentLocaleContextInterface
     */
    private $lugLocaleContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->lugLocaleContext = $this->createLocaleContextMock();
        $this->localeProvider = $this->createLocaleProviderMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->localeContext = new LocaleContext(
            $this->lugLocaleContext,
            $this->localeProvider,
            $this->parameterResolver
        );
    }

    public function testLocales()
    {
        $this->lugLocaleContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue($locales = ['FR_fr']));

        $this->assertSame($locales, $this->localeContext->getLocales());
    }

    public function testFallbackLocale()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($locale = $this->createLocaleMock()));

        $locale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($code = 'en'));

        $this->assertSame($code, $this->localeContext->getFallbackLocale());
    }

    public function testFallbackLocaleWithApiRequest()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->assertNull($this->localeContext->getFallbackLocale());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ComponentLocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->getMock(BaseLocaleContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->getMock(LocaleProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->getMock(LocaleInterface::class);
    }
}
