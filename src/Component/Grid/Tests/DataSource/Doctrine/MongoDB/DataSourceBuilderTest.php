<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\DataSource\Doctrine\MongoDB;

use Doctrine\MongoDB\Iterator;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Lug\Component\Grid\DataSource\ArrayDataSource;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\DataSource\Doctrine\MongoDB\DataSourceBuilder;
use Lug\Component\Grid\DataSource\Doctrine\MongoDB\ExpressionBuilder;
use Lug\Component\Grid\DataSource\PagerfantaDataSource;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\Repository;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DataSourceBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataSourceBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Repository
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Builder
     */
    private $queryBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists(Builder::class)) {
            $this->markTestSkipped();
        }

        $this->repository = $this->createRepositoryMock();
        $this->queryBuilder = $this->createQueryBuilderMock();

        $this->repository
            ->expects($this->once())
            ->method('createQueryBuilderForCollection')
            ->will($this->returnValue($this->queryBuilder));

        $this->builder = new DataSourceBuilder($this->repository);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(DataSourceBuilderInterface::class, $this->builder);
    }

    public function testSelect()
    {
        $this->assertSame($this->builder, $this->builder->select('select'));
    }

    public function testInnerJoin()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($join = 'property'))
            ->will($this->returnSelf());

        $this->queryBuilder
            ->expects($this->once())
            ->method('notEqual')
            ->with($this->isNull())
            ->will($this->returnSelf());

        $this->assertSame($this->builder, $this->builder->innerJoin($join, 'alias'));
    }

    public function testLeftJoin()
    {
        $this->assertSame($this->builder, $this->builder->leftJoin('property', 'alias'));
    }

    public function testAndWhere()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('addAnd')
            ->with($this->identicalTo($where = 'expression'));

        $this->assertSame($this->builder, $this->builder->andWhere($where));
    }

    public function testOrWhere()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('addOr')
            ->with($this->identicalTo($where = 'expression'));

        $this->assertSame($this->builder, $this->builder->orWhere($where));
    }

    public function testOrderBy()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo($sort = 'expression'),
                $this->identicalTo('ASC')
            );

        $this->assertSame($this->builder, $this->builder->orderBy($sort));
    }

    public function testOrderByWithOrder()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo($sort = 'expression'),
                $this->identicalTo($order = 'DESC')
            );

        $this->assertSame($this->builder, $this->builder->orderBy($sort, $order));
    }

    public function testParameter()
    {
        $this->assertSame($this->builder, $this->builder->setParameter('parameter', 'value'));
    }

    public function testPlaceholder()
    {
        $this->assertSame($value = 'foo', $this->builder->createPlaceholder('parameter', $value));
    }

    public function testProperty()
    {
        $this->repository
            ->expects($this->once())
            ->method('getProperty')
            ->with(
                $this->identicalTo($field = 'field'),
                $this->identicalTo($this->queryBuilder)
            )
            ->will($this->returnValue($property = 'property'));

        $this->assertSame($property, $this->builder->getProperty($field));
    }

    public function testAliases()
    {
        $this->assertEmpty($this->builder->getAliases());
    }

    public function testExpressionBuilder()
    {
        $this->assertInstanceOf(ExpressionBuilder::class, $this->builder->getExpressionBuilder());
    }

    public function testCreateDataSource()
    {
        $dataSource = $this->builder->createDataSource();

        $this->assertInstanceOf(PagerfantaDataSource::class, $dataSource);
        $this->assertInstanceOf(DoctrineODMMongoDBAdapter::class, $dataSource->getAdapter());

        $this->assertSame(10, $dataSource->getMaxPerPage());
        $this->assertSame(1, $dataSource->getCurrentPage());
    }

    public function testCreateDataSourceWithAllOption()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue($iterator = $this->createIteratorMock()));

        $iterator
            ->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($values = [new \stdClass()]));

        $dataSource = $this->builder->createDataSource(['all' => true]);

        $this->assertInstanceOf(ArrayDataSource::class, $dataSource);
        $this->assertSame($values, iterator_to_array($dataSource));
    }

    public function testLimit()
    {
        $this->assertSame($this->builder, $this->builder->setLimit($limit = 20));
        $this->assertSame($limit, $this->builder->createDataSource()->getMaxPerPage());
    }

    public function testPage()
    {
        $this->assertSame($this->builder, $this->builder->setPage($page = 1));
        $this->assertSame($page, $this->builder->createDataSource()->getCurrentPage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Repository
     */
    private function createRepositoryMock()
    {
        return $this->createMock(Repository::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Builder
     */
    private function createQueryBuilderMock()
    {
        return $this->createMock(Builder::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Query
     */
    private function createQueryMock()
    {
        return $this->createMock(Query::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Iterator
     */
    private function createIteratorMock()
    {
        return $this->createMock(Iterator::class);
    }
}
