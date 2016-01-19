<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Bundle\GridBundle\DataSource\Doctrine\MongoDB\DataSourceBuilder;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Repository extends DocumentRepository implements RepositoryInterface
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /***
     * @param DocumentManager   $dm
     * @param UnitOfWork        $uow
     * @param ClassMetadata     $class
     * @param ResourceInterface $resource
     */
    public function __construct(
        DocumentManager $dm,
        UnitOfWork $uow,
        ClassMetadata $class,
        ResourceInterface $resource
    ) {
        parent::__construct($dm, $uow, $class);

        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function findForIndex(array $criteria, array $orderBy = [])
    {
        return new Pagerfanta(new DoctrineODMMongoDBAdapter($this->buildQueryBuilder($criteria, $orderBy, true)));
    }

    /**
     * {@inheritdoc}
     */
    public function findForShow(array $criteria, array $orderBy = [])
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findForUpdate(array $criteria, array $orderBy = [])
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findForDelete(array $criteria, array $orderBy = [])
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = [])
    {
        return $this->buildQueryBuilder($criteria, $orderBy)->getQuery()->getSingleResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = [], $limit = null, $offset = null)
    {
        $queryBuilder = $this->buildQueryBuilder($criteria, $orderBy);

        if ($limit !== null) {
            $queryBuilder->limit($limit);
        }

        if ($offset !== null) {
            $queryBuilder->skip($offset);
        }

        return $queryBuilder->getQuery()->getIterator()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderForCollection()
    {
        return $this->createQueryBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function createDataSourceBuilder(array $options = [])
    {
        return new DataSourceBuilder($this, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($property, $root = null)
    {
        if (is_string($root) && !empty($root)) {
            return $root.'.'.$property;
        }

        return $property;
    }

    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     * @param bool     $collection
     *
     * @return Builder
     */
    protected function buildQueryBuilder(array $criteria, array $orderBy, $collection = false)
    {
        $queryBuilder = $collection ? $this->createQueryBuilderForCollection() : $this->createQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        return $queryBuilder;
    }

    /**
     * @param Builder $queryBuilder
     * @param mixed[] $criteria
     */
    private function applyCriteria(Builder $queryBuilder, array $criteria = null)
    {
        foreach ($criteria as $property => $value) {
            if ($value === null) {
                $queryBuilder->field($this->getProperty($property, $queryBuilder))->equals(null);
            } elseif (is_array($value)) {
                $queryBuilder->field($this->getProperty($property, $queryBuilder))->in($value);
            } elseif (!empty($value)) {
                $queryBuilder->field($this->getProperty($property, $queryBuilder))->equals($value);
            }
        }
    }

    /**
     * @param Builder  $queryBuilder
     * @param string[] $orderBy
     */
    private function applySorting(Builder $queryBuilder, array $orderBy = [])
    {
        foreach ($orderBy as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->sort($this->getProperty($property, $queryBuilder), $order);
            }
        }
    }
}
