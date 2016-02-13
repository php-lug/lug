<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Batch;

use Lug\Component\Grid\Batch\Type\TypeInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Batcher implements BatcherInterface
{
    /**
     * @var RegistryInterface
     */
    private $batchRegistry;

    /**
     * @var mixed[]
     */
    private $cache = [];

    /**
     * @param RegistryInterface $batchRegistry
     */
    public function __construct(RegistryInterface $batchRegistry)
    {
        $this->batchRegistry = $batchRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function batch(GridInterface $grid, $batch, $data)
    {
        $batch = $grid->getBatch($batch);
        $types = $this->resolveTypes($batch->getType());

        if (!isset($this->cache[$hash = spl_object_hash($grid).':'.spl_object_hash($batch)])) {
            $resolver = new OptionsResolver();

            foreach ($types as $type) {
                $type->configureOptions($resolver);
            }

            $this->cache[$hash] = $resolver->resolve(array_merge(
                ['batch' => $batch, 'grid' => $grid],
                $batch->getOptions()
            ));
        }

        reset($types)->batch($data, $this->cache[$hash]);
    }

    /**
     * @param string $type
     *
     * @return TypeInterface[]
     */
    private function resolveTypes($type)
    {
        $batchTypes = [];

        do {
            $batchTypes[] = $batchType = $this->batchRegistry[$type];
        } while (($type = $batchType->getParent()) !== null);

        return $batchTypes;
    }
}
