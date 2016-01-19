<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\DataSource\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\Query\Builder;
use Lug\Bundle\GridBundle\DataSource\ArrayDataSource;
use Lug\Bundle\GridBundle\DataSource\PagerfantaDataSource;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\MongoDB\Repository;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;

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
     * @var Builder
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
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function innerJoin($join, $alias)
    {
        $this->queryBuilder->field($join)->notEqual(null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function leftJoin($join, $alias)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function andWhere($where)
    {
        $this->queryBuilder->addAnd($where);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orWhere($where)
    {
        $this->queryBuilder->addOr($where);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($sort, $order = 'ASC')
    {
        $this->queryBuilder->sort($sort, $order);

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
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createPlaceholder($parameter, $value, $type = null)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($field)
    {
        return $this->repository->getProperty($field, $this->queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        if ($this->expressionBuilder === null) {
            $this->expressionBuilder = new ExpressionBuilder(clone $this->queryBuilder);
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
            return new ArrayDataSource($queryBuilder->getQuery()->getIterator()->toArray());
        }

        $dataSource = new PagerfantaDataSource(new DoctrineODMMongoDBAdapter($queryBuilder));
        $dataSource->setMaxPerPage($this->limit);
        $dataSource->setCurrentPage($this->page);

        return $dataSource;
    }
}
