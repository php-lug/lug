<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\DataSource\Doctrine\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lug\Bundle\GridBundle\DataSource\ArrayDataSource;
use Lug\Bundle\GridBundle\DataSource\Doctrine\ORM\DataSourceBuilder;
use Lug\Bundle\GridBundle\DataSource\Doctrine\ORM\ExpressionBuilder;
use Lug\Bundle\GridBundle\DataSource\PagerfantaDataSource;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\Repository;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private $queryBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
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
        $this->queryBuilder
            ->expects($this->once())
            ->method('addSelect')
            ->with($this->identicalTo($select = 'alias'));

        $this->assertSame($this->builder, $this->builder->select($select));
    }

    public function testInnerJoin()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('innerJoin')
            ->with(
                $this->identicalTo($join = 'property'),
                $this->identicalTo($alias = 'alias')
            );

        $this->assertSame($this->builder, $this->builder->innerJoin($join, $alias));
    }

    public function testLeftJoin()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with(
                $this->identicalTo($join = 'property'),
                $this->identicalTo($alias = 'alias')
            );

        $this->assertSame($this->builder, $this->builder->leftJoin($join, $alias));
    }

    public function testAndWhere()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->identicalTo($where = 'expression'));

        $this->assertSame($this->builder, $this->builder->andWhere($where));
    }

    public function testOrWhere()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('orWhere')
            ->with($this->identicalTo($where = 'expression'));

        $this->assertSame($this->builder, $this->builder->orWhere($where));
    }

    public function testOrderBy()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('addOrderBy')
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
            ->method('addOrderBy')
            ->with(
                $this->identicalTo($sort = 'expression'),
                $this->identicalTo($order = 'DESC')
            );

        $this->assertSame($this->builder, $this->builder->orderBy($sort, $order));
    }

    public function testParameter()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter = 'parameter'),
                $this->identicalTo($value = 'value')
            );

        $this->assertSame($this->builder, $this->builder->setParameter($parameter, $value));
    }

    public function testParameterWithType()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter = 'parameter'),
                $this->identicalTo($value = 'value'),
                $this->identicalTo($type = 'type')
            );

        $this->assertSame($this->builder, $this->builder->setParameter($parameter, $value, $type));
    }

    public function testPlaceholder()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->matchesRegularExpression('/'.($pattern = 'foo_bar_[a-z0-9]{22}').'/'),
                $this->identicalTo($value = 'value')
            );

        $this->assertRegExp('/\:'.$pattern.'/', $this->builder->createPlaceholder('foo.bar', $value));
    }

    public function testPlaceholderUnicity()
    {
        $this->queryBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->with(
                $this->matchesRegularExpression('/'.($pattern = 'foo_bar_[a-z0-9]{22}').'/'),
                $this->identicalTo($value = 'value')
            );

        $this->assertRegExp(
            $resultPattern = '/\:'.$pattern.'/',
            $placeholder1 = $this->builder->createPlaceholder('foo.bar', $value)
        );

        $this->assertRegExp($resultPattern, $placeholder2 = $this->builder->createPlaceholder('foo.bar', $value));
        $this->assertNotSame($placeholder1, $placeholder2);
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
        $this->queryBuilder
            ->expects($this->once())
            ->method('getAllAliases')
            ->will($this->returnValue($aliases = ['foo']));

        $this->assertSame($aliases, $this->builder->getAliases());
    }

    public function testExpressionBuilder()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($this->createExprMock()));

        $this->assertInstanceOf(ExpressionBuilder::class, $this->builder->getExpressionBuilder());
    }

    public function testCreateDataSource()
    {
        $dataSource = $this->builder->createDataSource();

        $this->assertInstanceOf(PagerfantaDataSource::class, $dataSource);
        $this->assertInstanceOf(DoctrineORMAdapter::class, $dataSource->getAdapter());

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
            ->method('getResult')
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
        return $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private function createQueryBuilderMock()
    {
        return $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Query
     */
    private function createQueryMock()
    {
        return $this->getMock(\stdClass::class, ['getResult']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Expr
     */
    private function createExprMock()
    {
        return $this->getMock(Expr::class);
    }
}
