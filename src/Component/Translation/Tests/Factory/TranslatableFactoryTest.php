<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Tests\Factory;

use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Factory\TranslatableFactory;
use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslatableTrait;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatableFactory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private $localeContext;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->localeContext = $this->createLocaleContextMock();

        $this->factory = new TranslatableFactory($this->resource, $this->localeContext);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Factory::class, $this->factory);
    }

    public function testCreate()
    {
        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($translatableClass = TranslatableTest::class));

        $this->resource
            ->expects($this->once())
            ->method('getTranslation')
            ->will($this->returnValue($translationResource = $this->createResourceMock()));

        $translationResource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($translationClass = TranslationTest::class));

        $this->localeContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue($locales = ['fr', 'es']));

        $this->localeContext
            ->expects($this->once())
            ->method('getFallbackLocale')
            ->will($this->returnValue($fallbackLocale = 'en'));

        $translatable = $this->factory->create();

        $this->assertInstanceOf($translatableClass, $translatable);
        $this->assertSame($locales, $translatable->getLocales());
        $this->assertSame($fallbackLocale, $translatable->getFallbackLocale());
        $this->assertSame($translationClass, $translatable->getTranslationClass());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->getMock(LocaleContextInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTest implements TranslatableInterface
{
    use TranslatableTrait;
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationTest implements TranslationInterface
{
    use TranslationTrait;
}
