<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Batch\Type;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Grid\Batch\Type\AbstractType;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Exception\DomainException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DeleteType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $domainManagerRegistry;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param RegistryInterface          $domainManagerRegistry
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(
        RegistryInterface $domainManagerRegistry,
        ParameterResolverInterface $parameterResolver
    ) {
        $this->domainManagerRegistry = $domainManagerRegistry;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function batch($data, array $options)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            return;
        }

        $grid = $options['grid'];
        $domainManager = $this->domainManagerRegistry[$grid->getResource()->getName()];
        $api = $this->parameterResolver->resolveApi();

        foreach ($data as $object) {
            try {
                $domainManager->delete($object, !$api);
            } catch (DomainException $e) {
                if ($api) {
                    throw $e;
                }
            }
        }

        if ($api) {
            $domainManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'delete';
    }
}
