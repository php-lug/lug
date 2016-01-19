<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\EventSubscriber\Batch\Doctrine\MongoDB;

use Lug\Bundle\GridBundle\Form\EventSubscriber\Batch\AbstractGridBatchSubscriber;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridBatchSubscriber extends AbstractGridBatchSubscriber
{
    /**
     * {@inheritdoc}
     */
    protected function findChoices(GridInterface $grid, RepositoryInterface $repository, array $choices)
    {
        $result = $repository
            ->createQueryBuilderForCollection()
            ->field($repository->getProperty($grid->getResource()->getIdPropertyPath()))
            ->in($choices)
            ->getQuery()
            ->getIterator()
            ->toArray();

        return count($result) === count($choices) ? $result : [];
    }
}
