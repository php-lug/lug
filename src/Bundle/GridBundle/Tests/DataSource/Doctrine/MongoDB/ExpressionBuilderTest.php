<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\DataSource\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use Lug\Bundle\GridBundle\DataSource\Doctrine\MongoDB\ExpressionBuilder;
use Lug\Component\Grid\DataSource\ExpressionBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionBuilder
     */
    private $expressionBuilder;

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

        $this->queryBuilder = $this->createQueryBuilderMock();
        $this->expressionBuilder = new ExpressionBuilder($this->queryBuilder);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ExpressionBuilderInterface::class, $this->expressionBuilder);
    }

    public function testAndX()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->exactly(2))
            ->method('addAnd')
            ->withConsecutive(
                [$expression1 = 'expression1'],
                [$expression2 = 'expression2']
            );

        $this->assertSame($expr, $this->expressionBuilder->andX([$expression1, $expression2]));
    }

    public function testOrX()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->exactly(2))
            ->method('addOr')
            ->withConsecutive(
                [$expression1 = 'expression1'],
                [$expression2 = 'expression2']
            );

        $this->assertSame($expr, $this->expressionBuilder->orX([$expression1, $expression2]));
    }

    public function testAsc()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo('ASC')
            )
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->asc($x));
    }

    public function testDesc()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo('DESC')
            )
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->desc($x));
    }

    public function testEq()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($y = 'value'))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->eq($x, $y));
    }

    public function testNeq()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('notEqual')
            ->with($this->identicalTo($y = 'value'))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->neq($x, $y));
    }

    public function testLt()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('lt')
            ->with($this->identicalTo($y = 'value'))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->lt($x, $y));
    }

    public function testLte()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('lte')
            ->with($this->identicalTo($y = 'value'))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->lte($x, $y));
    }

    public function testGt()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('gt')
            ->with($this->identicalTo($y = 'value'))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->gt($x, $y));
    }

    public function testGte()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('gte')
            ->with($this->identicalTo($y = 'value'))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->gte($x, $y));
    }

    public function testExists()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('exists')
            ->with($this->identicalTo(true))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->exists($x));
    }

    public function testIn()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('in')
            ->with($this->identicalTo($y = ['value']))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->in($x, $y));
    }

    public function testNotIn()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('notIn')
            ->with($this->identicalTo($y = ['value']))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->notIn($x, $y));
    }

    public function testIsNull()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo(null))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->isNull($x));
    }

    public function testIsNotNull()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('notEqual')
            ->with($this->identicalTo(null))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->isNotNull($x));
    }

    /**
     * @dataProvider likeProvider
     */
    public function testLike($like, $regex)
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('equals')
            ->with($this->callback(function ($parameter) use ($regex) {
                return $parameter instanceof \MongoRegex && (string) $parameter === $regex;
            }))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->like($x, $like));
    }

    /**
     * @dataProvider likeProvider
     */
    public function testNotLike($like, $regex)
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->once())
            ->method('not')
            ->with($this->callback(function ($parameter) use ($regex) {
                return $parameter instanceof \MongoRegex && (string) $parameter === $regex;
            }))
            ->will($this->returnSelf());

        $this->assertSame($expr, $this->expressionBuilder->notLike($x, $like));
    }

    public function testBetween()
    {
        $this->queryBuilder
            ->expects($this->exactly(3))
            ->method('expr')
            ->willReturnOnConsecutiveCalls(
                $gteExpr = $this->createExprMock(),
                $lteExpr = $this->createExprMock(),
                $expr = $this->createExprMock()
            );

        $gteExpr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($value = 'property'))
            ->will($this->returnSelf());

        $gteExpr
            ->expects($this->once())
            ->method('gte')
            ->with($this->identicalTo($x = 'from'))
            ->will($this->returnSelf());

        $lteExpr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($value))
            ->will($this->returnSelf());

        $lteExpr
            ->expects($this->once())
            ->method('lte')
            ->with($this->identicalTo($y = 'to'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->exactly(2))
            ->method('addAnd')
            ->withConsecutive([$gteExpr], [$lteExpr]);

        $this->assertSame($expr, $this->expressionBuilder->between($value, $x, $y));
    }

    public function testNotBetween()
    {
        $this->queryBuilder
            ->expects($this->exactly(3))
            ->method('expr')
            ->willReturnOnConsecutiveCalls(
                $gteExpr = $this->createExprMock(),
                $lteExpr = $this->createExprMock(),
                $expr = $this->createExprMock()
            );

        $gteExpr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($value = 'property'))
            ->will($this->returnSelf());

        $gteExpr
            ->expects($this->once())
            ->method('lt')
            ->with($this->identicalTo($x = 'from'))
            ->will($this->returnSelf());

        $lteExpr
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo($value))
            ->will($this->returnSelf());

        $lteExpr
            ->expects($this->once())
            ->method('gt')
            ->with($this->identicalTo($y = 'to'))
            ->will($this->returnSelf());

        $expr
            ->expects($this->exactly(2))
            ->method('addOr')
            ->withConsecutive([$gteExpr], [$lteExpr]);

        $this->assertSame($expr, $this->expressionBuilder->notBetween($value, $x, $y));
    }

    /**
     * @return mixed[]
     */
    public function likeProvider()
    {
        return [
            'none'  => ['foo', '/^foo$/'],
            'start' => ['%foo', '/^.*?foo$/'],
            'end'   => ['foo%', '/^foo.*?$/'],
            'both'  => ['%foo%', '/^.*?foo.*?$/'],
        ];
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Expr
     */
    private function createExprMock()
    {
        return $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
