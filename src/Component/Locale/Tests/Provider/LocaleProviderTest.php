<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Provider;

use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProvider;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleProviderTest extends \PHPUNit_Framework_TestCase
{
    /**
     * @var LocaleProvider
     */
    private $localeProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->defaultLocale = 'en';
        $this->localeRepository = $this->createLocaleRepositoryMock();
        $this->localeProvider = new LocaleProvider($this->localeRepository, $this->defaultLocale);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleProviderInterface::class, $this->localeProvider);
    }

    public function testLocales()
    {
        $this->localeRepository
            ->expects($this->once())
            ->method('findBy')
            ->with($this->identicalTo(['enabled' => true]))
            ->will($this->returnValue($locales = [$this->createLocaleMock()]));

        $this->assertSame($locales, $this->localeProvider->getLocales());
    }

    public function testRequiredLocales()
    {
        $this->localeRepository
            ->expects($this->once())
            ->method('findBy')
            ->with($this->identicalTo(['enabled' => true]))
            ->will($this->returnValue([
                $requiredLocale = $this->createLocaleMock(),
                $optionalLocale = $this->createLocaleMock(),
            ]));

        $requiredLocale
            ->expects($this->once())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $optionalLocale
            ->expects($this->once())
            ->method('isRequired')
            ->will($this->returnValue(false));

        $this->assertSame([$requiredLocale], $this->localeProvider->getRequiredLocales());
    }

    public function testDefaultLocale()
    {
        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->identicalTo(['enabled' => true, 'code' => $this->defaultLocale]))
            ->will($this->returnValue($defaultLocale = $this->createLocaleMock()));

        $this->assertSame($defaultLocale, $this->localeProvider->getDefaultLocale());
    }

    /**
     * @expectedException \Lug\Component\Locale\Exception\LocaleNotFoundException
     * @expectedExceptionMessage The default locale "en" could not be found.
     */
    public function testInvalidDefaultLocale()
    {
        $this->localeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->identicalTo(['enabled' => true, 'code' => $this->defaultLocale]))
            ->will($this->returnValue(null));

        $this->localeProvider->getDefaultLocale();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->getMock(LocaleInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createLocaleRepositoryMock()
    {
        return $this->getMock(RepositoryInterface::class);
    }
}
