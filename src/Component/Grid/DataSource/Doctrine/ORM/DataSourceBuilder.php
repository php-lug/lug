<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\DataSource\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Lug\Component\Grid\DataSource\ArrayDataSource;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\DataSource\PagerfantaDataSource;
use Lug\Component\Resource\Repository\Doctrine\ORM\Repository;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DataSourceBuilder implements DataSourceBuilderInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var ExpressionBuilder
     */
    private $expressionBuilder;

    /**
     * @var int
     */
    private $limit = 10;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @param Repository $repository
     * @param mixed[]    $options
     */
    public function __construct(Repository $repository, array $options = [])
    {
        $repositoryMethod = isset($options['repository_method'])
            ? $options['repository_method']
            : 'createQueryBuilderForCollection';

        $this->repository = $repository;
        $this->queryBuilder = $this->repository->$repositoryMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function select($select)
    {
        $this->queryBuilder->addSelect($select);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function innerJoin($join, $alias)
    {
        $this->queryBuilder->innerJoin($join, $alias);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function leftJoin($join, $alias)
    {
        $this->queryBuilder->leftJoin($join, $alias);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function andWhere($where)
    {
        $this->queryBuilder->andWhere($where);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orWhere($where)
    {
        $this->queryBuilder->orWhere($where);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($sort, $order = 'ASC')
    {
        $this->queryBuilder->addOrderBy($sort, $order);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($parameter, $value, $type = null)
    {
        $this->queryBuilder->setParameter($parameter, $value, $type);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createPlaceholder($parameter, $value, $type = null)
    {
        $placeholder = str_replace('.', '_', $parameter).'_'.str_replace('.', '', uniqid(null, true));
        $this->setParameter($placeholder, $value, $type);

        return ':'.$placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($field, $root = null)
    {
        return $this->repository->getProperty($field, $root ?: $this->queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return $this->queryBuilder->getAllAliases();
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        if ($this->expressionBuilder === null) {
            $this->expressionBuilder = new ExpressionBuilder($this->queryBuilder->expr());
        }

        return $this->expressionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createDataSource(array $options = [])
    {
        $queryBuilder = clone $this->queryBuilder;

        if (isset($options['all']) && $options['all']) {
            return new ArrayDataSource($queryBuilder->getQuery()->getResult());
        }

        $dataSource = new PagerfantaDataSource(new DoctrineORMAdapter(
            $queryBuilder,
            isset($options['fetch_join_collection']) ? $options['fetch_join_collection'] : true,
            isset($options['use_output_walkers']) ? $options['use_output_walkers'] : true
        ));

        $dataSource->setMaxPerPage($this->limit);
        $dataSource->setCurrentPage($this->page);

        return $dataSource;
    }
}
