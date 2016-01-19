<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\DataSource;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ExpressionBuilderInterface
{
    /**
     * @param string[] $expressions
     *
     * @return string
     */
    public function andX(array $expressions);

    /**
     * @param string[] $expressions
     *
     * @return string
     */
    public function orX(array $expressions);

    /**
     * @param mixed $x
     *
     * @return string
     */
    public function asc($x);

    /**
     * @param mixed $x
     *
     * @return string
     */
    public function desc($x);

    /**
     * @param mixed $x
     * @param mixed $y
     *
     * @return string
     */
    public function eq($x, $y);

    /**
     * @param mixed $x
     * @param mixed $y
     *
     * @return string
     */
    public function neq($x, $y);

    /**
     * @param mixed $x
     * @param mixed $y
     *
     * @return string
     */
    public function lt($x, $y);

    /**
     * @param mixed $x
     * @param mixed $y
     *
     * @return string
     */
    public function lte($x, $y);

    /**
     * @param mixed $x
     * @param mixed $y
     *
     * @return string
     */
    public function gt($x, $y);

    /**
     * @param mixed $x
     * @param mixed $y
     *
     * @return string
     */
    public function gte($x, $y);

    /**
     * @param mixed $x
     *
     * @return string
     */
    public function exists($x);

    /**
     * @param string $x
     * @param mixed  $y
     *
     * @return string
     */
    public function in($x, $y);

    /**
     * @param string $x
     * @param mixed  $y
     *
     * @return string
     */
    public function notIn($x, $y);

    /**
     * @param string $x
     *
     * @return string
     */
    public function isNull($x);

    /**
     * @param string $x
     *
     * @return string
     */
    public function isNotNull($x);

    /**
     * @param string $x
     * @param mixed  $y
     *
     * @return string
     */
    public function like($x, $y);

    /**
     * @param string $x
     * @param mixed  $y
     *
     * @return string
     */
    public function notLike($x, $y);

    /**
     * @param mixed $value
     * @param int   $x
     * @param int   $y
     *
     * @return string
     */
    public function between($value, $x, $y);

    /**
     * @param mixed $value
     * @param int   $x
     * @param int   $y
     *
     * @return string
     */
    public function notBetween($value, $x, $y);
}
