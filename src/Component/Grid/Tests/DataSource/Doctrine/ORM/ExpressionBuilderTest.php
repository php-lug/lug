<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\DataSource\Doctrine\ORM;

use Doctrine\ORM\Query\Expr;
use Lug\Component\Grid\DataSource\Doctrine\ORM\ExpressionBuilder;
use Lug\Component\Grid\DataSource\ExpressionBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Expr
     */
    private $expr;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->expr = $this->createExprMock();
        $this->builder = new ExpressionBuilder($this->expr);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ExpressionBuilderInterface::class, $this->builder);
    }

    public function testAndX()
    {
        $this->expr
            ->expects($this->once())
            ->method('andX')
            ->with(
                $this->identicalTo($expression1 = 'expression1'),
                $this->identicalTo($expression2 = 'expression2')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->andX([$expression1, $expression2]));
    }

    public function testOrX()
    {
        $this->expr
            ->expects($this->once())
            ->method('orX')
            ->with(
                $this->identicalTo($expression1 = 'expression1'),
                $this->identicalTo($expression2 = 'expression2')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->orX([$expression1, $expression2]));
    }

    public function testAsc()
    {
        $this->expr
            ->expects($this->once())
            ->method('asc')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->asc($x));
    }

    public function testDesc()
    {
        $this->expr
            ->expects($this->once())
            ->method('desc')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->desc($x));
    }

    public function testEq()
    {
        $this->expr
            ->expects($this->once())
            ->method('eq')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->eq($x, $y));
    }

    public function testNeq()
    {
        $this->expr
            ->expects($this->once())
            ->method('neq')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->neq($x, $y));
    }

    public function testLt()
    {
        $this->expr
            ->expects($this->once())
            ->method('lt')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->lt($x, $y));
    }

    public function testLte()
    {
        $this->expr
            ->expects($this->once())
            ->method('lte')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->lte($x, $y));
    }

    public function testGt()
    {
        $this->expr
            ->expects($this->once())
            ->method('gt')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->gt($x, $y));
    }

    public function testGte()
    {
        $this->expr
            ->expects($this->once())
            ->method('gte')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->gte($x, $y));
    }

    public function testExists()
    {
        $this->expr
            ->expects($this->once())
            ->method('exists')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->exists($x));
    }

    public function testIn()
    {
        $this->expr
            ->expects($this->once())
            ->method('in')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->in($x, $y));
    }

    public function testNotIn()
    {
        $this->expr
            ->expects($this->once())
            ->method('notIn')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->notIn($x, $y));
    }

    public function testIsNull()
    {
        $this->expr
            ->expects($this->once())
            ->method('isNull')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->isNull($x));
    }

    public function testIsNotNull()
    {
        $this->expr
            ->expects($this->once())
            ->method('isNotNull')
            ->with($this->identicalTo($x = 'property'))
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->isNotNull($x));
    }

    public function testLike()
    {
        $this->expr
            ->expects($this->once())
            ->method('like')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->like($x, $y));
    }

    public function testNotLike()
    {
        $this->expr
            ->expects($this->once())
            ->method('notLike')
            ->with(
                $this->identicalTo($x = 'property'),
                $this->identicalTo($y = 'placeholder')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->notLike($x, $y));
    }

    public function testBetween()
    {
        $this->expr
            ->expects($this->once())
            ->method('between')
            ->with(
                $this->identicalTo($value = 'property'),
                $this->identicalTo($x = 'placeholder1'),
                $this->identicalTo($y = 'placeholder2')
            )
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->between($value, $x, $y));
    }

    public function testNotBetween()
    {
        $this->expr
            ->expects($this->once())
            ->method('between')
            ->with(
                $this->identicalTo($value = 'property'),
                $this->identicalTo($x = 'placeholder1'),
                $this->identicalTo($y = 'placeholder2')
            )
            ->will($this->returnValue($betweenExpression = 'between_expression'));

        $this->expr
            ->expects($this->once())
            ->method('not')
            ->with($this->identicalTo($betweenExpression))
            ->will($this->returnValue($expression = 'expression'));

        $this->assertSame($expression, $this->builder->notBetween($value, $x, $y));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Expr
     */
    private function createExprMock()
    {
        return $this->getMock(Expr::class);
    }
}
