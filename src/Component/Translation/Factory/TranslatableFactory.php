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
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @param ResourceInterface         $resource
     * @param PropertyAccessorInterface $propertyAccessor
     * @param LocaleContextInterface    $localeContext
     */
    public function __construct(
        ResourceInterface $resource,
        PropertyAccessorInterface $propertyAccessor,
        LocaleContextInterface $localeContext
    ) {
        parent::__construct($resource, $propertyAccessor);

        $this->resource = $resource;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        return parent::create(array_merge($options, [
            'locales'          => $this->localeContext->getLocales(),
            'fallbackLocale'   => $this->localeContext->getFallbackLocale(),
            'translationClass' => $this->resource->getRelation('translation')->getModel(),
        ]));
    }
}
