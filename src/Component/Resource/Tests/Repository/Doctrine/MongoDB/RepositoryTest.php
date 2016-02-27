<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Repository\Doctrine\MongoDB;

use Doctrine\MongoDB\Iterator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Component\Grid\DataSource\Doctrine\MongoDB\DataSourceBuilder;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\Repository;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
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
     * @var \PHPUnit_Framework_MockObject_MockObject|DocumentManager
     */
    private $documentManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UnitOfWork
     */
    private $unitOfWork;

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
        if (!class_exists(DocumentManager::class)) {
            $this->markTestSkipped();
        }

        $this->documentManager = $this->createDocumentManagerMock();
        $this->unitOfWork = $this->createUnitOfWorkMock();
        $this->classMetadata = $this->createClassMetadataMock();
        $this->resource = $this->createResourceMock();
        $this->class = $this->classMetadata->name = \stdClass::class;

        $this->repository = new Repository(
            $this->documentManager,
            $this->unitOfWork,
            $this->classMetadata,
            $this->resource
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(DocumentRepository::class, $this->repository);
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

    public function testCreateQueryForCollectionBuilder()
    {
        $queryBuilder = $this->setUpQueryBuilder();

        $this->assertSame($queryBuilder, $this->repository->createQueryBuilderForCollection());
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
            ->method('getSingleResult')
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
            ->method('getSingleResult')
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
            ->method('getSingleResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findOneBy(['foo' => $value], ['baz' => 'ASC']));
    }

    public function testFindBy()
    {
        $queryBuilder = $this->setUpQueryBuilder($value = 'bar');

        $queryBuilder
            ->expects($this->once())
            ->method('limit')
            ->with($this->identicalTo($limit = 5))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('skip')
            ->with($this->identicalTo($offset = 10))
            ->will($this->returnSelf());

        $queryBuilder
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
            ->will($this->returnValue($result = ['result']));

        $this->assertSame($result, $this->repository->findBy(['foo' => $value], ['baz' => 'ASC'], $limit, $offset));
    }

    public function testFindForIndex()
    {
        $this->setUpQueryBuilder($value = 'bar');

        $pager = $this->repository->findForIndex(['foo' => $value], ['baz' => 'ASC']);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertInstanceOf(DoctrineODMMongoDBAdapter::class, $pager->getAdapter());
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
            ->method('getSingleResult')
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
            ->method('getSingleResult')
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
            ->method('getSingleResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->repository->findForDelete(['foo' => $value], ['baz' => 'ASC']));
    }

    /**
     * @param mixed       $value
     * @param string|null $alias
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Builder
     */
    private function setUpQueryBuilder($value = false, $alias = null)
    {
        $this->documentManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder = $this->createQueryBuilderMock()));

        if ($value === false) {
            return $queryBuilder;
        }

        $queryBuilder
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo('foo'))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method($value === null || is_string($value) ? 'equals' : 'in')
            ->with($this->identicalTo($value))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo('baz'),
                $this->identicalTo('ASC')
            )
            ->will($this->returnSelf());

        return $queryBuilder;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DocumentManager
     */
    private function createDocumentManagerMock()
    {
        return $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UnitOfWork
     */
    private function createUnitOfWorkMock()
    {
        return $this->getMockBuilder(UnitOfWork::class)
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Builder
     */
    private function createQueryBuilderMock()
    {
        return $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Query
     */
    private function createQueryMock()
    {
        return $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Iterator
     */
    private function createIteratorMock()
    {
        return $this->getMock(Iterator::class);
    }
}
