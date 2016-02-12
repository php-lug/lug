<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\RegistryBundle\DependencyInjection\Compiler;

use Lug\Bundle\RegistryBundle\Exception\TagAttributeNotFoundException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRegisterRegistryPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $registry;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @param string $registry
     * @param string $tag
     * @param string $attribute
     */
    public function __construct($registry, $tag, $attribute = 'alias')
    {
        $this->registry = $registry;
        $this->tag = $tag;
        $this->attribute = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition($this->registry);

        foreach ($container->findTaggedServiceIds($this->tag) as $service => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute[$this->attribute])) {
                    throw new TagAttributeNotFoundException(sprintf(
                        'The attribute "%s" could not be found for the tag "%s" on the "%s" service.',
                        $this->attribute,
                        $this->tag,
                        $service
                    ));
                }

                $registry->addMethodCall('offsetSet', [$attribute[$this->attribute], new Reference($service)]);
            }
        }
    }
}
