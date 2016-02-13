<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Sort\Type;

use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var RegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     * @param RegistryInterface $repositoryRegistry
     */
    public function __construct(
        RegistryInterface $resourceRegistry,
        RegistryInterface $repositoryRegistry
    ) {
        $this->resourceRegistry = $resourceRegistry;
        $this->repositoryRegistry = $repositoryRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function sort($data, array $options)
    {
        $builder = $options['builder'];

        if (!in_array($options['field'], $builder->getAliases(), true)) {
            $builder->leftJoin($builder->getProperty($options['field']), $options['field']);
        }

        $resource = $options['resource'];
        $repository = $this->repositoryRegistry[$resource->getName()];

        $builder->orderBy($repository->getProperty($options['field'], $resource->getLabelPropertyPath()), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('resource')
            ->setAllowedTypes('resource', ['string', ResourceInterface::class])
            ->setNormalizer('resource', function (Options $options, $resource) {
                return is_string($resource) ? $this->resourceRegistry[$resource] : $resource;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'resource';
    }
}
