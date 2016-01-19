<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Form\Type;

use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractResourceType extends AbstractType
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param ResourceInterface $resource
     * @param FactoryInterface  $factory
     */
    public function __construct(ResourceInterface $resource, FactoryInterface $factory)
    {
        $this->resource = $resource;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'resource' => $this->resource,
            'factory'  => $this->factory,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ResourceType::class;
    }
}
