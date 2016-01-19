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
class TextType extends AbstractType
{
    const TYPE_CONTAINS = 'contains';
    const TYPE_NOT_CONTAINS = 'not_contains';
    const TYPE_EMPTY = 'empty';
    const TYPE_NOT_EMPTY = 'not_empty';
    const TYPE_EQUALS = 'equals';
    const TYPE_NOT_EQUALS = 'not_equals';
    const TYPE_STARTS_WITH = 'starts_with';
    const TYPE_NOT_STARTS_WITH = 'not_starts_with';
    const TYPE_ENDS_WITH = 'ends_with';
    const TYPE_NOT_ENDS_WITH = 'not_ends_with';

    /**
     * @return string[]
     */
    public static function getTypes()
    {
        return array_merge(self::getSimpleTypes(), self::getEmptyTypes());
    }

    /**
     * @return string[]
     */
    public static function getSimpleTypes()
    {
        return [
            self::TYPE_CONTAINS,
            self::TYPE_NOT_CONTAINS,
            self::TYPE_EQUALS,
            self::TYPE_NOT_EQUALS,
            self::TYPE_STARTS_WITH,
            self::TYPE_NOT_STARTS_WITH,
            self::TYPE_ENDS_WITH,
            self::TYPE_NOT_ENDS_WITH,
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
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    protected function process($field, $data, array $options)
    {
        $builder = $options['builder'];

        switch ($data['type']) {
            case self::TYPE_CONTAINS:
                return $builder->getExpressionBuilder()->like(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, '%'.$data['value'].'%')
                );

            case self::TYPE_NOT_CONTAINS:
                return $builder->getExpressionBuilder()->notLike(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, '%'.$data['value'].'%')
                );

            case self::TYPE_EMPTY:
                return $builder->getExpressionBuilder()->isNull($builder->getProperty($field));

            case self::TYPE_NOT_EMPTY:
                return $builder->getExpressionBuilder()->isNotNull($builder->getProperty($field));

            case self::TYPE_NOT_EQUALS:
                return $builder->getExpressionBuilder()->neq(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_STARTS_WITH:
                return $builder->getExpressionBuilder()->like(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'].'%')
                );

            case self::TYPE_NOT_STARTS_WITH:
                return $builder->getExpressionBuilder()->notLike(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, $data['value'].'%')
                );

            case self::TYPE_ENDS_WITH:
                return $builder->getExpressionBuilder()->like(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, '%'.$data['value'])
                );

            case self::TYPE_NOT_ENDS_WITH:
                return $builder->getExpressionBuilder()->notLike(
                    $builder->getProperty($field),
                    $builder->createPlaceholder($field, '%'.$data['value'])
                );
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
                && is_string($data['value'])
            ) || in_array($data['type'], self::getEmptyTypes(), true));
    }
}
