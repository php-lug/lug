<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Factory;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Factory implements FactoryInterface
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param ResourceInterface         $resource
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(ResourceInterface $resource, PropertyAccessorInterface $propertyAccessor)
    {
        $this->resource = $resource;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $class = $this->resource->getModel();
        $object = new $class();

        foreach ($options as $propertyPath => $value) {
            $this->propertyAccessor->setValue($object, $propertyPath, $value);
        }

        return $object;
    }
}
