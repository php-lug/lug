<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column\Type;

use Lug\Component\Grid\Column\ColumnRendererInterface;
use Lug\Component\Grid\Model\Column;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceType extends AbstractType
{
    /**
     * @var ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var ColumnRendererInterface
     */
    private $renderer;

    /**
     * @var Column[]
     */
    private $cache = [];

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param ServiceRegistryInterface  $resourceRegistry
     * @param ColumnRendererInterface   $renderer
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        ServiceRegistryInterface $resourceRegistry,
        ColumnRendererInterface $renderer
    ) {
        parent::__construct($propertyAccessor);

        $this->resourceRegistry = $resourceRegistry;
        $this->propertyAccessor = $propertyAccessor;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        if (($resource = $this->getValue($data, $options)) === null) {
            return;
        }

        if (!isset($this->cache[$hash = spl_object_hash($options['grid']).':'.spl_object_hash($options['column'])])) {
            $this->cache[$hash] = new Column($options['resource_path'], null, $options['type'], $options['options']);
        }

        return $this->renderer->render($options['grid'], $this->cache[$hash], $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['resource', 'type'])
            ->setDefaults([
                'options'       => [],
                'resource_path' => function (Options $options, $resourcePath) {
                    return $resourcePath === null ? $options['resource']->getLabelPropertyPath() : $resourcePath;
                },
            ])
            ->setNormalizer('resource', function (Options $options, $resource) {
                return is_string($resource) ? $this->resourceRegistry[$resource] : $resource;
            })
            ->setAllowedTypes('resource', ['string', ResourceInterface::class])
            ->setAllowedTypes('resource_path', 'string')
            ->setAllowedTypes('type', 'string')
            ->setAllowedTypes('options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'resource';
    }
}
