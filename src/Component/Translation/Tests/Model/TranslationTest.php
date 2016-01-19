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

use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConcreteTranslation
     */
    private $translation;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->translation = new ConcreteTranslation();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TranslationInterface::class, $this->translation);
        $this->assertTrue(in_array(TranslationTrait::class, class_uses($this->translation), true));
    }

    public function testDefaultState()
    {
        $this->assertNull($this->translation->getTranslatable());
        $this->assertNull($this->translation->getLocale());
    }

    public function testAttachTranslatable()
    {
        $translatable = $this->createTranslatableMock();
        $translatable
            ->expects($this->once())
            ->method('addTranslation')
            ->with($this->identicalTo($this->translation));

        $this->translation->setTranslatable($translatable);

        $this->assertSame($translatable, $this->translation->getTranslatable());
    }

    public function testDetachTranslatable()
    {
        $translatable = $this->createTranslatableMock();
        $translatable
            ->expects($this->once())
            ->method('addTranslation')
            ->with($this->identicalTo($this->translation));

        $translatable
            ->expects($this->once())
            ->method('removeTranslation')
            ->with($this->identicalTo($this->translation));

        $this->translation->setTranslatable($translatable);
        $this->translation->setTranslatable(null);

        $this->assertNull($this->translation->getTranslatable());
    }

    public function testSyncTranslatable()
    {
        $oldTranslatable = $this->createTranslatableMock();
        $oldTranslatable
            ->expects($this->once())
            ->method('addTranslation')
            ->with($this->identicalTo($this->translation));

        $oldTranslatable
            ->expects($this->once())
            ->method('removeTranslation')
            ->with($this->identicalTo($this->translation));

        $newTranslatable = $this->createTranslatableMock();
        $newTranslatable
            ->expects($this->once())
            ->method('addTranslation')
            ->with($this->identicalTo($this->translation));

        $this->translation->setTranslatable($oldTranslatable);
        $this->translation->setTranslatable($newTranslatable);

        $this->assertSame($newTranslatable, $this->translation->getTranslatable());
    }

    public function testLocale()
    {
        $this->translation->setLocale($locale = 'fr_FR');

        $this->assertSame($locale, $this->translation->getLocale());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslatableInterface
     */
    private function createTranslatableMock()
    {
        return $this->getMock(TranslatableInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConcreteTranslation implements TranslationInterface
{
    use TranslationTrait;
}
