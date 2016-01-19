<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Action;

use Lug\Component\Grid\Action\Type\TypeInterface;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionRenderer implements ActionRendererInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $actionRegistry;

    /**
     * @var mixed[]
     */
    private $cache = [];

    /**
     * @param ServiceRegistryInterface $actionRegistry
     */
    public function __construct(ServiceRegistryInterface $actionRegistry)
    {
        $this->actionRegistry = $actionRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function render(GridViewInterface $grid, ActionInterface $action, $data)
    {
        $types = $this->resolveTypes($action->getType());

        if (!isset($this->cache[$hash = spl_object_hash($grid).':'.spl_object_hash($action)])) {
            $resolver = new OptionsResolver();

            foreach ($types as $type) {
                $type->configureOptions($resolver);
            }

            $this->cache[$hash] = $resolver->resolve(array_merge(
                ['action' => $action, 'grid' => $grid],
                $action->getOptions()
            ));
        }

        return reset($types)->render($data, $this->cache[$hash]);
    }

    /**
     * @param string $type
     *
     * @return TypeInterface[]
     */
    private function resolveTypes($type)
    {
        $actionTypes = [];

        do {
            $actionTypes[] = $actionType = $this->actionRegistry[$type];
        } while (($type = $actionType->getParent()) !== null);

        return $actionTypes;
    }
}
