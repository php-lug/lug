<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Batch;

use Lug\Component\Grid\Batch\BatcherInterface as BaseBatcherInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Batcher implements BatcherInterface
{
    /**
     * @var BaseBatcherInterface
     */
    private $batcher;

    /**
     * @param BaseBatcherInterface $batcher
     */
    public function __construct(BaseBatcherInterface $batcher)
    {
        $this->batcher = $batcher;
    }

    /**
     * {@inheritdoc}
     */
    public function batch(GridInterface $grid, FormInterface $form)
    {
        if ($form->isValid()) {
            $this->batcher->batch(
                $grid,
                $form->get('type')->getData(),
                $form->get('value')->getData()
            );
        }
    }
}
