<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Filter\Type;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeType extends AbstractType
{
    const TYPE_GREATER_THAN_OR_EQUALS = 'greater_than_or_equals';
    const TYPE_GREATER_THAN = 'greater_than';
    const TYPE_LESS_THAN_OR_EQUALS = 'less_than_or_equals';
    const TYPE_LESS_THAN = 'less_than';
    const TYPE_EQUALS = 'equals';
    const TYPE_NOT_EQUALS = 'not_equals';
    const TYPE_BETWEEN = 'between';
    const TYPE_NOT_BETWEEN = 'not_between';
    const TYPE_EMPTY = 'empty';
    const TYPE_NOT_EMPTY = 'not_empty';

    /**
     * @return string[]
     */
    public static function getTypes()
    {
        return array_merge(self::getSimpleTypes(), self::getCompoundTypes(), self::getEmptyTypes());
    }

    /**
     * @return string[]
     */
    public static function getSimpleTypes()
    {
        return [
            self::TYPE_GREATER_THAN_OR_EQUALS,
            self::TYPE_GREATER_THAN,
            self::TYPE_LESS_THAN_OR_EQUALS,
            self::TYPE_LESS_THAN,
            self::TYPE_EQUALS,
            self::TYPE_NOT_EQUALS,
        ];
    }

    /**
     * @return string[]
     */
    public static function getCompoundTypes()
    {
        return [
            self::TYPE_BETWEEN,
            self::TYPE_NOT_BETWEEN,
        ];
    }

    /**
     * @return string[]
     */
    public static function getEmptyTypes()
    {
        return [
            self::TYPE_EMPTY,
            self::TYPE_NOT_EMPTY,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'datetime';
    }

    /**
     * {@inheritdoc}
     */
    protected function process($field, $data, array $options)
    {
        $builder = $options['builder'];

        switch ($data['type']) {
            case self::TYPE_GREATER_THAN_OR_EQUALS:
                return $builder->getExpressionBuilder()->gte(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_GREATER_THAN:
                return $builder->getExpressionBuilder()->gt(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_LESS_THAN_OR_EQUALS:
                return $builder->getExpressionBuilder()->lte(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_LESS_THAN:
                return $builder->getExpressionBuilder()->lt(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_NOT_EQUALS:
                return $builder->getExpressionBuilder()->neq(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_BETWEEN:
                $expressionBuilder = $builder->getExpressionBuilder();

                return $expressionBuilder->between(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['from']),
                    $builder->createPlaceholder($field, $data['to'])
                );

            case self::TYPE_NOT_BETWEEN:
                $expressionBuilder = $builder->getExpressionBuilder();

                return $expressionBuilder->notBetween(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['from']),
                    $builder->createPlaceholder($field, $data['to'])
                );

            case self::TYPE_EMPTY:
                return $builder->getExpressionBuilder()->isNull($builder->getProperty($field));

            case self::TYPE_NOT_EMPTY:
                return $builder->getExpressionBuilder()->isNotNull($builder->getProperty($field));
        }

        return $builder->getExpressionBuilder()->eq(
            $builder->getProperty($field),
            $builder->createPlaceholder($field, $data['value'])
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function validate($data, array $options)
    {
        return parent::validate($data, $options)
            && is_array($data)
            && isset($data['type'])
            && ((
                in_array($data['type'], self::getSimpleTypes(), true)
                && isset($data['value'])
                && $data['value'] instanceof \DateTimeInterface
            ) || (
                in_array($data['type'], self::getCompoundTypes(), true)
                && isset($data['from'])
                && isset($data['to'])
                && $data['from'] instanceof \DateTimeInterface
                && $data['to'] instanceof \DateTimeInterface
            ) || in_array($data['type'], self::getEmptyTypes(), true));
    }
}
