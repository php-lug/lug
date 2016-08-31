<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Lug\Bundle\ResourceBundle\Exception\TagAttributeNotFoundException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRegisterGenericDomainListenerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $listener;

    /**
     * @var mixed[]
     */
    private $event;

    /**
     * @var string[]
     */
    private $actions;

    /**
     * @var string[]
     */
    private $prefixes;

    /**
     * AbstractRegisterGenericDomainListenerPass constructor.
     *
     * @param string   $listener
     * @param mixed[]  $event
     * @param string[] $actions
     * @param string[] $prefixes
     */
    public function __construct($listener, array $event, array $actions, array $prefixes)
    {
        $this->listener = $listener;
        $this->event = $event;
        $this->actions = $actions;
        $this->prefixes = $prefixes;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $flashListener = $container->getDefinition($this->listener);
        $controllers = $container->findTaggedServiceIds($tag = 'lug.controller');

        foreach ($controllers as $controller => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute[$alias = 'resource'])) {
                    throw new TagAttributeNotFoundException(sprintf(
                        'The attribute "%s" could not be found for the tag "%s" on the "%s" service.',
                        $alias,
                        $tag,
                        $controller
                    ));
                }

                foreach ($this->actions as $action) {
                    foreach ($this->prefixes as $prefix) {
                        $flashListener->addTag('lug.resource.domain.event_listener', array_merge([
                            'event' => 'lug.'.$attribute['resource'].'.'.$prefix.'_'.$action,
                        ], $this->event));
                    }
                }
            }
        }
    }
}
