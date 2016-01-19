<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Lug\Component\Translation\Exception\LocaleNotFoundException;
use Lug\Component\Translation\Exception\TranslationNotFoundException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait TranslatableTrait
{
    /**
     * @var string[]
     */
    private $locales;

    /**
     * @var string|null
     */
    private $fallbackLocale;

    /**
     * @var string
     */
    private $translationClass;

    /**
     * @var TranslationInterface[]|Collection
     */
    protected $translations;

    /**
     * @return string|null
     */
    public function getLocale()
    {
        try {
            return $this->getTranslation()->getLocale();
        } catch (TranslationNotFoundException $e) {
        }
    }

    /**
     * @return string[]
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * @param string[] $locales
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
    }

    /**
     * @return bool
     */
    public function hasFallbackLocale()
    {
        return $this->fallbackLocale !== null;
    }

    /**
     * @return string|null
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * @param string|null $fallbackLocale
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * @return string
     */
    public function getTranslationClass()
    {
        return $this->translationClass;
    }

    /**
     * @param string $translationClass
     */
    public function setTranslationClass($translationClass)
    {
        $this->translationClass = $translationClass;
    }

    /**
     * @return TranslationInterface[]|Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param bool $allowCreate
     *
     * @return TranslationInterface
     */
    public function getTranslation($allowCreate = false)
    {
        $locales = $this->getLocales();

        if (empty($locales)) {
            throw new LocaleNotFoundException();
        }

        $translation = null;

        foreach ($locales as $locale) {
            if (($translation = $this->translations->get($locale)) !== null) {
                break;
            }
        }

        if ($translation === null && $allowCreate) {
            $class = $this->getTranslationClass();

            $translation = new $class();
            $translation->setLocale(reset($locales));

            $this->addTranslation($translation);
        }

        if ($translation === null && $this->hasFallbackLocale()) {
            $translation = $this->translations->get($this->getFallbackLocale());
        }

        if ($translation === null) {
            if ($this->hasFallbackLocale()) {
                $locales[] = $this->getFallbackLocale();
            }

            throw new TranslationNotFoundException();
        }

        return $translation;
    }

    /**
     * @param TranslationInterface $translation
     */
    public function addTranslation(TranslationInterface $translation)
    {
        if (!$this->translations->containsKey($translation->getLocale())) {
            $this->translations->set($translation->getLocale(), $translation);
            $translation->setTranslatable($this);
        }
    }

    /**
     * @param TranslationInterface $translation
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }
    }

    private function initTranslatable()
    {
        $this->translations = new ArrayCollection();
    }
}
