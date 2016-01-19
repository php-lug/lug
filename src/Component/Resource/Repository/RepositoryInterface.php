<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RepositoryInterface extends ObjectRepository
{
    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     *
     * @return object[]
     */
    public function findForIndex(array $criteria, array $orderBy = []);

    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     *
     * @return object|null
     */
    public function findForShow(array $criteria, array $orderBy = []);

    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     *
     * @return object|null
     */
    public function findForUpdate(array $criteria, array $orderBy = []);

    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     *
     * @return object|null
     */
    public function findForDelete(array $criteria, array $orderBy = []);

    /**
     * @param mixed[] $options
     *
     * @return DataSourceBuilderInterface
     */
    public function createDataSourceBuilder(array $options = []);

    /**
     * @param string      $property
     * @param string|null $root
     *
     * @return string
     */
    public function getProperty($property, $root = null);
}
