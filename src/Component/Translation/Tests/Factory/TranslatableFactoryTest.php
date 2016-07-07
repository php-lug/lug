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
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Factory\TranslatableFactory;
use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslatableTrait;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $translationFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->localeContext = $this->createLocaleContextMock();
        $this->translationFactory = $this->createFactoryMock();

        $this->factory = new TranslatableFactory(
            $this->resource,
            $this->propertyAccessor,
            $this->localeContext,
            $this->translationFactory
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Factory::class, $this->factory);
    }

    public function testCreateWithOptions()
    {
        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($translatableClass = TranslatableTest::class));

        $this->localeContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue($locales = ['fr', 'es']));

        $this->localeContext
            ->expects($this->once())
            ->method('getFallbackLocale')
            ->will($this->returnValue($fallbackLocale = 'en'));

        $translatable = $this->factory->create(['name' => $name = 'foo']);

        $this->assertInstanceOf($translatableClass, $translatable);
        $this->assertSame($name, $translatable->getName());
        $this->assertSame($locales, $translatable->getLocales());
        $this->assertSame($fallbackLocale, $translatable->getFallbackLocale());
        $this->assertSame($this->translationFactory, $translatable->getTranslationFactory());
    }

    public function testCreateWithoutOptions()
    {
        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($translatableClass = TranslatableTest::class));

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
        $this->assertSame($this->translationFactory, $translatable->getTranslationFactory());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->createMock(LocaleContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createFactoryMock()
    {
        return $this->createMock(FactoryInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTest implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationTest implements TranslationInterface
{
    use TranslationTrait;
}
