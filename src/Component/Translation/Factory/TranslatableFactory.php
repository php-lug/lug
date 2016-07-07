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

use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableFactory extends Factory
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var RegistryInterface
     */
    private $translationFactory;

    /**
     * @param ResourceInterface         $resource
     * @param PropertyAccessorInterface $propertyAccessor
     * @param LocaleContextInterface    $localeContext
     * @param FactoryInterface          $translationFactory
     */
    public function __construct(
        ResourceInterface $resource,
        PropertyAccessorInterface $propertyAccessor,
        LocaleContextInterface $localeContext,
        FactoryInterface $translationFactory
    ) {
        parent::__construct($resource, $propertyAccessor);

        $this->localeContext = $localeContext;
        $this->translationFactory = $translationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        return parent::create(array_merge($options, [
            'locales'            => $this->localeContext->getLocales(),
            'fallbackLocale'     => $this->localeContext->getFallbackLocale(),
            'translationFactory' => $this->translationFactory,
        ]));
    }
}
