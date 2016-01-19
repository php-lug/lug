<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lug\Bundle\GridBundle\DataSource\Doctrine\ORM\DataSourceBuilder;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\Repository;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    private $entityManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private $classMetadata;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var string
     */
    private $class;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->createEntityManagerMock();
        $this->classMetadata = $this->createClassMetadataMock();
        $this->resource = $this->createResourceMock();
        $this->class = $this->classMetadata->name = \stdClass::class;

        $this->repository = new Repository($this->entityManager, $this->classMetadata, $this->resource);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityRepository::class, $this->repository);
    }

    public function testCreateDataSourceBuilder()
    {
        $this->setUpQueryBuilder();
        $dataSourceBuilder = $this->repository->createDataSourceBuilder();

        $this->assertInstanceOf(DataSourceBuilder::class, $dataSourceBuilder);
    }

    public function testCreateQueryBuilder()
    {
        $queryBuilder = $this->setUpQueryBuilder();

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilder());
    }

    public function testCreateQueryBuilderWithAlias()
    {
        $queryBuilder = $this->setUpQueryBuilder(false, $alias = 'foo');

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilder($alias));
    }

    public function testCreateQueryBuilderWithIndexBy()
    {
        $queryBuilder = $this->setUpQueryBuilder(false, null, $indexBy = 'index');

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilder(null, $indexBy));
    }

    public function testCreateQueryForCollectionBuilder()
    {
        $queryBuilder = $this->setUpQueryBuilder();

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilderForCollection());
    }

    public function testCreateQueryBuilderForCollectionWithAlias()
    {
        $queryBuilder = $this->setUpQueryBuilder(false, $alias = 'foo');

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilderForCollection($alias));
    }

    public function testCreateQueryBuilderForCollectionWithIndexBy()
    {
        $queryBuilder = $this->setUpQueryBuilder(false, null, $indexBy = 'index');

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilderForCollection(null, $indexBy));
    }

    public function testFindOneByWithNullValue()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = null);

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findOneBy(['foo' => $value], ['baz' => 'ASC']));
    }

    public function testFindOneByWithScalarValue()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = 'bar');

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findOneBy(['foo' => $value], ['baz' => 'ASC']));
    }

    public function testFindOneByWithArrayValue()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = ['bar']);

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findOneBy(['foo' => $value], ['baz' => 'ASC']));
    }

    public function testFindBy()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = 'bar');

        $queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($this->identicalTo($limit = 5))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with($this->identicalTo($offset = 10))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($result = ['result']));

        $this->assertSame($result, $this->repository->findBy(['foo' => $value], ['baz' => 'ASC'], $limit, $offset));
    }

    public function testFindForIndex()
    {
        $this->setUpQueryBuilder($value = 'bar');

        $pager = $this->repository->findForIndex(['foo' => $value], ['baz' => 'ASC']);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertInstanceOf(DoctrineORMAdapter::class, $pager->getAdapter());
    }

    public function testFindForShow()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = 'bar');

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findForShow(['foo' => $value], ['baz' => 'ASC']));
    }

    public function testFindForUpdate()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = 'bar');

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findForUpdate(['foo' => $value], ['baz' => 'ASC']));
    }

    public function testFindForDelete()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = 'bar');

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findForDelete(['foo' => $value], ['baz' => 'ASC']));
    }

    /**
     * @param mixed       $value
     * @param string|null $alias
     * @param string|null $indexBy
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private function setUpQueryBuilder($value = false, $alias = null, $indexBy = null)
    {
        if ($alias === null) {
            $this->resource
                ->expects($this->once())
                ->method('getName')
                ->will($this->returnValue($resource = 'resource'));
        } else {
            $resource = $alias;
        }

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder = $this->createQueryBuilderMock()));

        $queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with($this->identicalTo($resource))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with($this->identicalTo($this->class), $this->identicalTo($resource), $this->identicalTo($indexBy))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->any())
            ->method('getRootAliases')
            ->will($this->returnValue([$resource]));

        if ($value === false) {
            return $queryBuilder;
        }

        $queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        if ($value === null) {
            $expr
                ->expects($this->once())
                ->method('isNull')
                ->with($this->identicalTo($resource.'.'.($property = 'foo')))
                ->will($this->returnValue($expression = 'expression'));
        } else {
            $expr
                ->expects($this->once())
                ->method(is_string($value) ? 'eq' : 'in')
                ->with(
                    $this->identicalTo($resource.'.'.($property = 'foo')),
                    $this->matchesRegularExpression('/:'.$resource.'_'.$property.'_[a-z0-9]{22}/')
                )
                ->will($this->returnValue($expression = 'expression'));

            $queryBuilder
                ->expects($this->once())
                ->method('setParameter')
                ->with(
                    $this->matchesRegularExpression('/'.$resource.'_'.$property.'_[a-z0-9]{22}/'),
                    $this->identicalTo($value)
                )
                ->will($this->returnSelf());
        }

        $queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->identicalTo($expression))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('addOrderBy')
            ->with($this->identicalTo($resource.'.'.($order = 'baz')), $this->identicalTo($sort = 'ASC'))
            ->will($this->returnSelf());

        return $queryBuilder;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    private function createEntityManagerMock()
    {
        return $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function createClassMetadataMock()
    {
        return $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private function createQueryBuilderMock()
    {
        return $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Expr
     */
    private function createExprMock()
    {
        return $this->getMock(Expr::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Query
     */
    private function createQueryMock()
    {
        return $this->getMock(\stdClass::class, ['getOneOrNullResult', 'getResult']);
    }
}
