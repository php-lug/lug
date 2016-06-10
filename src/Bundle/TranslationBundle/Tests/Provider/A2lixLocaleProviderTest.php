<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\Provider;

use Lug\Bundle\TranslationBundle\Provider\A2lixLocaleProvider;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class A2lixLocaleProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var A2lixLocaleProvider
     */
    private $a2lixLocaleProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->localeProvider = $this->createLocaleProviderMock();

        $this->a2lixLocaleProvider = new A2lixLocaleProvider($this->localeProvider);
    }

    public function testLocales()
    {
        $enLocale = $this->createLocaleMock();
        $enLocale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($enCode = 'en'));

        $frLocale = $this->createLocaleMock();
        $frLocale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($frCode = 'fr'));

        $this->localeProvider
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue([$enLocale, $frLocale]));

        $this->assertSame([$enCode, $frCode], $this->a2lixLocaleProvider->getLocales());
    }

    public function testDefaultLocale()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($locale = $this->createLocaleMock()));

        $locale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($code = 'en'));

        $this->assertSame($code, $this->a2lixLocaleProvider->getDefaultLocale());
    }

    public function testRequiredLocales()
    {
        $locale = $this->createLocaleMock();
        $locale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($code = 'fr'));

        $this->localeProvider
            ->expects($this->once())
            ->method('getRequiredLocales')
            ->will($this->returnValue([$locale]));

        $this->assertSame([$code], $this->a2lixLocaleProvider->getRequiredLocales());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->createMock(LocaleProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->createMock(LocaleInterface::class);
    }
}
