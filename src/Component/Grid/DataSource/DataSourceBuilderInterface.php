<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\DataSource;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface DataSourceBuilderInterface
{
    /**
     * @param string|string[] $select
     *
     * @return DataSourceBuilderInterface
     */
    public function select($select);

    /**
     * @param string $join
     * @param string $alias
     *
     * @return DataSourceBuilderInterface
     */
    public function innerJoin($join, $alias);

    /**
     * @param string $join
     * @param string $alias
     *
     * @return DataSourceBuilderInterface
     */
    public function leftJoin($join, $alias);

    /**
     * @param string $where
     *
     * @return DataSourceBuilderInterface
     */
    public function andWhere($where);

    /**
     * @param string $where
     *
     * @return DataSourceBuilderInterface
     */
    public function orWhere($where);

    /**
     * @param string $sort
     * @param string $order
     *
     * @return DataSourceBuilderInterface
     */
    public function orderBy($sort, $order = 'ASC');

    /**
     * @param int $limit
     *
     * @return DataSourceBuilderInterface
     */
    public function setLimit($limit);

    /**
     * @param int $page
     *
     * @return DataSourceBuilderInterface
     */
    public function setPage($page);

    /**
     * @param string      $parameter
     * @param mixed       $value
     * @param string|null $type
     *
     * @return DataSourceBuilderInterface
     */
    public function setParameter($parameter, $value, $type = null);

    /**
     * @param string      $parameter
     * @param mixed       $value
     * @param string|null $type
     *
     * @return string
     */
    public function createPlaceholder($parameter, $value, $type = null);

    /**
     * @param string $field
     *
     * @return string
     */
    public function getProperty($field);

    /**
     * @return string[]
     */
    public function getAliases();

    /**
     * @return ExpressionBuilderInterface
     */
    public function getExpressionBuilder();

    /**
     * @param mixed[] $options
     *
     * @return DataSourceInterface
     */
    public function createDataSource(array $options = []);
}
