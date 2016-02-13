<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Filter;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Filter\Type\TypeInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Filterer implements FiltererInterface
{
    /**
     * @var RegistryInterface
     */
    private $filterRegistry;

    /**
     * @param RegistryInterface $filterRegistry
     */
    public function __construct(RegistryInterface $filterRegistry)
    {
        $this->filterRegistry = $filterRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(DataSourceBuilderInterface $builder, GridInterface $grid, array $filters = [])
    {
        foreach ($filters as $name => $filter) {
            if (!$grid->hasFilter($name)) {
                continue;
            }

            $gridFilter = $grid->getFilter($name);
            $types = $this->resolveTypes($gridFilter->getType());
            $resolver = new OptionsResolver();

            foreach ($types as $type) {
                $type->configureOptions($resolver);
            }

            reset($types)->filter($filter, $resolver->resolve(array_merge(
                ['filter' => $gridFilter, 'grid' => $grid, 'builder' => $builder],
                $gridFilter->getOptions()
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
        $filterTypes = [];

        do {
            $filterTypes[] = $filterType = $this->filterRegistry[$type];
        } while (($type = $filterType->getParent()) !== null);

        return $filterTypes;
    }
}
