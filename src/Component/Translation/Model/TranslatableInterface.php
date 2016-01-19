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

use Doctrine\Common\Collections\Collection;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface TranslatableInterface
{
    /**
     * @return string|null
     */
    public function getLocale();

    /**
     * @return string[]
     */
    public function getLocales();

    /**
     * @param string[] $locales
     */
    public function setLocales($locales);

    /**
     * @return bool
     */
    public function hasFallbackLocale();

    /**
     * @return string|null
     */
    public function getFallbackLocale();

    /**
     * @param string|null $fallbackLocale
     */
    public function setFallbackLocale($fallbackLocale);

    /**
     * @return string
     */
    public function getTranslationClass();

    /**
     * @param string $translationClass
     */
    public function setTranslationClass($translationClass);

    /**
     * @return TranslationInterface[]|Collection
     */
    public function getTranslations();

    /**
     * @param bool $allowCreate
     *
     * @return TranslationInterface
     */
    public function getTranslation($allowCreate = false);

    /**
     * @param TranslationInterface $translation
     */
    public function addTranslation(TranslationInterface $translation);

    /**
     * @param TranslationInterface $translation
     */
    public function removeTranslation(TranslationInterface $translation);
}
