<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Lug\Component\Grid\DataSource\Doctrine\ORM\DataSourceBuilder;
use Lug\Component\Resource\Model\ResourceInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * WARNING - This trait should only be used with a class extending an EntityRepository.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
trait RepositoryTrait
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @param EntityManagerInterface $em
     * @param ClassMetadata          $class
     * @param ResourceInterface      $resource
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $class, ResourceInterface $resource)
    {
        parent::__construct($em, $class);

        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function findForIndex(array $criteria, array $orderBy = [])
    {
        return new Pagerfanta(new DoctrineORMAdapter(
            $this->buildQueryBuilder($criteria, $orderBy, true),
            false,
            false
        ));
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
        return $this->buildQueryBuilder($criteria, $orderBy)->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = [], $limit = null, $offset = null)
    {
        $queryBuilder = $this->buildQueryBuilder($criteria, $orderBy);

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        return parent::createQueryBuilder($alias ?: $this->getAlias(), $indexBy);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderForCollection($alias = null, $indexBy = null)
    {
        return $this->createQueryBuilder($alias, $indexBy);
    }

    /**
     * {@inheritdoc}
     */
    public function createDataSourceBuilder(array $options = [])
    {
        return new DataSourceBuilder($this, $options);
    }

    /**
     * @param string                   $property
     * @param QueryBuilder|string|null $root
     *
     * @return string
     */
    public function getProperty($property, $root = null)
    {
        return $this->getRootAlias($root).'.'.$property;
    }

    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     * @param bool     $collection
     *
     * @return QueryBuilder
     */
    protected function buildQueryBuilder(array $criteria, array $orderBy, $collection = false)
    {
        $queryBuilder = $collection ? $this->createQueryBuilderForCollection() : $this->createQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed[]      $criteria
     */
    private function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        foreach ($criteria as $property => $value) {
            if ($value === null) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull(
                    $this->getProperty($property, $queryBuilder)
                ));
            } elseif (is_array($value)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->in(
                        $property = $this->getProperty($property, $queryBuilder),
                        $this->createPlaceholder($parameter = $this->createParameter($property))
                    ))
                    ->setParameter($parameter, $value);
            } elseif ($value !== null) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq(
                        $property = $this->getProperty($property, $queryBuilder),
                        $this->createPlaceholder($parameter = $this->createParameter($property))
                    ))
                    ->setParameter($parameter, $value);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string[]     $orderBy
     */
    private function applySorting(QueryBuilder $queryBuilder, array $orderBy = [])
    {
        foreach ($orderBy as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getProperty($property, $queryBuilder), $order);
            }
        }
    }

    /**
     * @param string $property
     *
     * @return string
     */
    private function createParameter($property)
    {
        return str_replace('.', '_', $property).'_'.str_replace('.', '', uniqid(null, true));
    }

    /**
     * @param string $parameter
     *
     * @return string
     */
    private function createPlaceholder($parameter)
    {
        return ':'.$parameter;
    }

    /**
     * @param QueryBuilder|string|null $root
     *
     * @return string
     */
    private function getRootAlias($root)
    {
        if ($root instanceof QueryBuilder) {
            $root = $root->getRootAliases()[0];
        }

        return $root;
    }

    /**
     * @return string
     */
    private function getAlias()
    {
        return $this->resource->getName();
    }
}
