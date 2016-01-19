<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Sort;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Sort\Type\TypeInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Sorter implements SorterInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $sortRegistry;

    /**
     * @param ServiceRegistryInterface $sortRegistry
     */
    public function __construct(ServiceRegistryInterface $sortRegistry)
    {
        $this->sortRegistry = $sortRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function sort(DataSourceBuilderInterface $builder, GridInterface $grid, array $sorting)
    {
        foreach ($sorting as $name => $order) {
            if (!$grid->hasSort($name) || !in_array($order, [self::ASC, self::DESC], true)) {
                continue;
            }

            $sort = $grid->getSort($name);
            $types = $this->resolveTypes($sort->getType());
            $resolver = new OptionsResolver();

            foreach ($types as $type) {
                $type->configureOptions($resolver);
            }

            reset($types)->sort($order, $resolver->resolve(array_merge(
                ['builder' => $builder, 'grid' => $grid, 'sort' => $sort],
                $sort->getOptions()
            )));
        }
    }

    /**
     * @param string $type
     *
     * @return TypeInterface[]
     */
    private function resolveTypes($type)
    {
        $sortTypes = [];

        do {
            $sortTypes[] = $sortType = $this->sortRegistry[$type];
        } while (($type = $sortType->getParent()) !== null);

        return $sortTypes;
    }
}
