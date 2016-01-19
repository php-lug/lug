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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait TranslationTrait
{
    /**
     * @var TranslatableInterface
     */
    protected $translatable;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @return TranslatableInterface
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * @param TranslatableInterface $translatable
     */
    public function setTranslatable(TranslatableInterface $translatable = null)
    {
        $oldTranslatable = $this->translatable;
        $this->translatable = $translatable;

        if ($oldTranslatable !== null) {
            $oldTranslatable->removeTranslation($this);
        }

        if ($translatable !== null) {
            $translatable->addTranslation($this);
        }
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
