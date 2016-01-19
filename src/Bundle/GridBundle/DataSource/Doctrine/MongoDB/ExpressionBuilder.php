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
use Doctrine\ODM\MongoDB\Query\Expr;
use Lug\Component\Grid\DataSource\ExpressionBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var Builder
     */
    private $queryBuilder;

    /**
     * @param Builder $queryBuilder
     */
    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function andX(array $expressions)
    {
        $expr = $this->expr();

        foreach ($expressions as $expression) {
            $expr->addAnd($expression);
        }

        return $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function orX(array $expressions)
    {
        $expr = $this->expr();

        foreach ($expressions as $expression) {
            $expr->addOr($expression);
        }

        return $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function asc($x)
    {
        return $this->expr()->sort($x, 'ASC');
    }

    /**
     * {@inheritdoc}
     */
    public function desc($x)
    {
        return $this->expr()->sort($x, 'DESC');
    }

    /**
     * {@inheritdoc}
     */
    public function eq($x, $y)
    {
        return $this->field($x)->equals($y);
    }

    /**
     * {@inheritdoc}
     */
    public function neq($x, $y)
    {
        return $this->field($x)->notEqual($y);
    }

    /**
     * {@inheritdoc}
     */
    public function lt($x, $y)
    {
        return $this->field($x)->lt($y);
    }

    /**
     * {@inheritdoc}
     */
    public function lte($x, $y)
    {
        return $this->field($x)->lte($y);
    }

    /**
     * {@inheritdoc}
     */
    public function gt($x, $y)
    {
        return $this->field($x)->gt($y);
    }

    /**
     * {@inheritdoc}
     */
    public function gte($x, $y)
    {
        return $this->field($x)->gte($y);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($x)
    {
        return $this->field($x)->exists(true);
    }

    /**
     * {@inheritdoc}
     */
    public function in($x, $y)
    {
        return $this->field($x)->in($y);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($x, $y)
    {
        return $this->field($x)->notIn($y);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($x)
    {
        return $this->eq($x, null);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($x)
    {
        return $this->neq($x, null);
    }

    /**
     * {@inheritdoc}
     */
    public function like($x, $y)
    {
        return $this->eq($x, $this->convertLikeToRegex($y));
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($x, $y)
    {
        return $this->field($x)->not($this->convertLikeToRegex($y));
    }

    /**
     * {@inheritdoc}
     */
    public function between($value, $x, $y)
    {
        return $this->andX([
            $this->gte($value, $x),
            $this->lte($value, $y),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function notBetween($value, $x, $y)
    {
        return $this->orX([
            $this->lt($value, $x),
            $this->gt($value, $y),
        ]);
    }

    /**
     * @param string $field
     *
     * @return Expr
     */
    private function field($field)
    {
        return $this->expr()->field($field);
    }

    /**
     * @return Expr
     */
    private function expr()
    {
        return $this->queryBuilder->expr();
    }

    /**
     * @param string $x
     *
     * @return \MongoRegex
     */
    private function convertLikeToRegex($x)
    {
        $x = preg_quote($x);
        $pattern = '.*?';
        $regex = null;

        if (strpos($x, $like = '%') === 0) {
            $regex .= $pattern;
            $x = substr($x, 1);
        }

        if (strrpos($x, $like) === strlen($x) - 1) {
            $regex .= substr($x, 0, -1).$pattern;
        } else {
            $regex .= $x;
        }

        return new \MongoRegex('/^'.$regex.'$/');
    }
}
