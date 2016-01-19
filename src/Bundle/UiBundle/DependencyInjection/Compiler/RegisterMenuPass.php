<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\DependencyInjection\Compiler;

use Knp\Menu\ItemInterface;
use Lug\Bundle\UiBundle\Exception\TagAttributeNotFoundException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterMenuPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds($tag = 'lug.menu.builder') as $service => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new TagAttributeNotFoundException(sprintf(
                        'The attribute "alias" could not be found for the tag "%s" on the "%s" service.',
                        $tag,
                        $service
                    ));
                }

                $container->setDefinition($service.'.menu', $this->createMenuService($service, $attribute['alias']));
            }
        }
    }

    /**
     * @param string $service
     * @param string $alias
     *
     * @return Definition
     */
    private function createMenuService($service, $alias)
    {
        $definition = new Definition(ItemInterface::class);
        $definition->setFactory([new Reference($service), 'create']);
        $definition->addTag('knp_menu.menu', ['alias' => $alias]);

        return $definition;
    }
}
