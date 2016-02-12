<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column;

use Lug\Component\Grid\Column\Type\TypeInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnRenderer implements ColumnRendererInterface
{
    /**
     * @var RegistryInterface
     */
    private $columnRegistry;

    /**
     * @var mixed[]
     */
    private $cache = [];

    /**
     * @param RegistryInterface $columnRegistry
     */
    public function __construct(RegistryInterface $columnRegistry)
    {
        $this->columnRegistry = $columnRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function render(GridViewInterface $grid, ColumnInterface $column, $data)
    {
        $types = $this->resolveTypes($column->getType());

        if (!isset($this->cache[$hash = spl_object_hash($grid).':'.spl_object_hash($column)])) {
            $resolver = new OptionsResolver();

            foreach ($types as $type) {
                $type->configureOptions($resolver);
            }

            $this->cache[$hash] = $resolver->resolve(array_merge(
                ['column' => $column, 'grid' => $grid],
                $column->getOptions()
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
        $columnTypes = [];

        do {
            $columnTypes[] = $columnType = $this->columnRegistry[$type];
        } while (($type = $columnType->getParent()) !== null);

        return $columnTypes;
    }
}
