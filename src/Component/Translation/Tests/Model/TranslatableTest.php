<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslatableTrait;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConcreteTranslatable
     */
    private $translatable;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->translatable = new ConcreteTranslatable();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TranslatableInterface::class, $this->translatable);
        $this->assertTrue(in_array(TranslatableTrait::class, class_uses($this->translatable), true));
    }

    public function testDefaultState()
    {
        $this->assertEmpty($this->translatable->getLocales());
        $this->assertFalse($this->translatable->hasFallbackLocale());
        $this->assertNull($this->translatable->getFallbackLocale());
        $this->assertNull($this->translatable->getTranslationClass());
        $this->assertInstanceOf(ArrayCollection::class, $this->translatable->getTranslations());
        $this->assertTrue($this->translatable->getTranslations()->isEmpty());
    }

    public function testLocales()
    {
        $this->translatable->setLocales($locales = ['fr_FR']);

        $this->assertSame($locales, $this->translatable->getLocales());
        $this->assertNull($this->translatable->getLocale());
    }

    public function testFallbackLocale()
    {
        $this->translatable->setFallbackLocale($fallbackLocale = 'fr_FR');

        $this->assertTrue($this->translatable->hasFallbackLocale());
        $this->assertSame($fallbackLocale, $this->translatable->getFallbackLocale());
    }

    public function testTranslationClass()
    {
        $this->translatable->setTranslationClass($translationClass = ConcreteTranslatableTranslation::class);

        $this->assertSame($translationClass, $this->translatable->getTranslationClass());
    }

    public function testAddTranslation()
    {
        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale = 'fr_FR'));

        $translation
            ->expects($this->once())
            ->method('setTranslatable')
            ->with($this->identicalTo($this->translatable));

        $this->translatable->addTranslation($translation);

        $this->assertSame([$locale => $translation], $this->translatable->getTranslations()->toArray());
    }

    public function testAddExistentTranslation()
    {
        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale = 'fr_FR'));

        $translation
            ->expects($this->once())
            ->method('setTranslatable')
            ->with($this->identicalTo($this->translatable));

        $duplicatedTranslation = $this->createTranslationMock();
        $duplicatedTranslation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale));

        $duplicatedTranslation
            ->expects($this->never())
            ->method('setTranslatable');

        $this->translatable->addTranslation($translation);
        $this->translatable->addTranslation($duplicatedTranslation);

        $this->assertSame([$locale => $translation], $this->translatable->getTranslations()->toArray());
    }

    public function testRemoveTranslation()
    {
        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale = 'fr_FR'));

        $translation
            ->expects($this->exactly(2))
            ->method('setTranslatable')
            ->withConsecutive(
                [$this->identicalTo($this->translatable)],
                [$this->isNull()]
            );

        $this->translatable->addTranslation($translation);
        $this->translatable->removeTranslation($translation);

        $this->assertTrue($this->translatable->getTranslations()->isEmpty());
    }

    public function testRemoveNonexistentTranslation()
    {
        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale = 'fr_FR'));

        $translation
            ->expects($this->never())
            ->method('setTranslatable');

        $this->translatable->removeTranslation($translation);
    }

    public function testTranslationWithSingleLocale()
    {
        $this->translatable->setLocales([$locale = 'fr_FR']);

        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale));

        $this->translatable->addTranslation($translation);

        $this->assertSame($translation, $this->translatable->getTranslation());
    }

    public function testTranslationWithMultipleLocales()
    {
        $this->translatable->setLocales(['fr_FR', $locale = 'en_EN']);

        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale));

        $this->translatable->addTranslation($translation);

        $this->assertSame($translation, $this->translatable->getTranslation());
    }

    public function testTranslationWithFallbackLocale()
    {
        $this->translatable->setLocales(['en_EN']);
        $this->translatable->setFallbackLocale($fallbackLocale = 'fr_FR');

        $translation = $this->createTranslationMock();
        $translation
            ->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($fallbackLocale));

        $this->translatable->addTranslation($translation);

        $this->assertSame($translation, $this->translatable->getTranslation());
    }

    public function testTranslationCreated()
    {
        $this->translatable->setLocales([$locale = 'fr_FR']);
        $this->translatable->setFallbackLocale('en_EN');
        $this->translatable->setTranslationClass($translationClass = ConcreteTranslatableTranslation::class);

        $translation = $this->translatable->getTranslation(true);

        $this->assertInstanceOf($translationClass, $translation);
        $this->assertSame($locale, $translation->getLocale());
        $this->assertSame([$locale => $translation], $this->translatable->getTranslations()->toArray());
    }

    public function testLocale()
    {
        $this->translatable->setLocales([$locale = 'fr_FR']);
        $this->translatable->setFallbackLocale('en_EN');
        $this->translatable->setTranslationClass(ConcreteTranslatableTranslation::class);
        $this->translatable->getTranslation(true);

        $this->assertSame($locale, $this->translatable->getLocale());
    }

    public function testLocaleWithoutTranslation()
    {
        $this->translatable->setLocales(['fr_FR']);
        $this->translatable->setFallbackLocale('en_EN');

        $this->assertNull($this->translatable->getLocale());
    }

    /**
     * @expectedException \Lug\Component\Translation\Exception\LocaleNotFoundException
     * @expectedExceptionMessage The locale could not be found.
     */
    public function testTranslationWithMissingLocales()
    {
        $this->translatable->getTranslation();
    }

    /**
     * @expectedException \Lug\Component\Translation\Exception\TranslationNotFoundException
     * @expectedExceptionMessage The translation could not be found.
     */
    public function testTranslationWithMissingFallbackLocale()
    {
        $this->translatable->setLocales(['fr_FR']);

        $this->translatable->getTranslation();
    }

    /**
     * @expectedException \Lug\Component\Translation\Exception\TranslationNotFoundException
     * @expectedExceptionMessage The translation could not be found.
     */
    public function testMissingTranslation()
    {
        $this->translatable->setLocales(['fr_FR', 'en_EN']);
        $this->translatable->setFallbackLocale('en');

        $this->translatable->getTranslation();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslationInterface
     */
    private function createTranslationMock()
    {
        return $this->createMock(TranslationInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConcreteTranslatable implements TranslatableInterface
{
    use TranslatableTrait;

    public function __construct()
    {
        $this->initTranslatable();
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConcreteTranslatableTranslation implements TranslationInterface
{
    use TranslationTrait;
}
