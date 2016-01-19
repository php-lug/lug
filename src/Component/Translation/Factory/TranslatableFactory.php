<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Factory;

use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableFactory extends Factory
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param ResourceInterface      $resource
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(ResourceInterface $resource, LocaleContextInterface $localeContext)
    {
        parent::__construct($resource);

        $this->resource = $resource;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $translatable = parent::create($options);

        $translatable->setLocales($this->localeContext->getLocales());
        $translatable->setFallbackLocale($this->localeContext->getFallbackLocale());
        $translatable->setTranslationClass($this->resource->getTranslation()->getModel());

        return $translatable;
    }
}
