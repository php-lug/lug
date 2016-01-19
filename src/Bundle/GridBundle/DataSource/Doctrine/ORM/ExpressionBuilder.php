<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\DataSource\Doctrine\ORM;

use Doctrine\ORM\Query\Expr;
use Lug\Component\Grid\DataSource\ExpressionBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var Expr
     */
    private $expr;

    /**
     * @param Expr $expr
     */
    public function __construct(Expr $expr)
    {
        $this->expr = $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function andX(array $expressions)
    {
        return call_user_func_array([$this->expr, 'andX'], $expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function orX(array $expressions)
    {
        return call_user_func_array([$this->expr, 'orX'], $expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function asc($x)
    {
        return $this->expr->asc($x);
    }

    /**
     * {@inheritdoc}
     */
    public function desc($x)
    {
        return $this->expr->desc($x);
    }

    /**
     * {@inheritdoc}
     */
    public function eq($x, $y)
    {
        return $this->expr->eq($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function neq($x, $y)
    {
        return $this->expr->neq($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function lt($x, $y)
    {
        return $this->expr->lt($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function lte($x, $y)
    {
        return $this->expr->lte($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function gt($x, $y)
    {
        return $this->expr->gt($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function gte($x, $y)
    {
        return $this->expr->gte($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($x)
    {
        return $this->expr->exists($x);
    }

    /**
     * {@inheritdoc}
     */
    public function in($x, $y)
    {
        return $this->expr->in($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($x, $y)
    {
        return $this->expr->notIn($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($x)
    {
        return $this->expr->isNull($x);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($x)
    {
        return $this->expr->isNotNull($x);
    }

    /**
     * {@inheritdoc}
     */
    public function like($x, $y)
    {
        return $this->expr->like($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($x, $y)
    {
        return $this->expr->notLike($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function between($value, $x, $y)
    {
        return $this->expr->between($value, $x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public function notBetween($value, $x, $y)
    {
        return $this->expr->not($this->between($value, $x, $y));
    }
}
