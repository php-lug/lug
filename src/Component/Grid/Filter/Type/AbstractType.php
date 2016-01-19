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

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractType implements TypeInterface
{
    const CONDITION_AND = 'AND';
    const CONDITION_OR = 'OR';

    /**
     * {@inheritdoc}
     */
    public function filter($data, array $options)
    {
        if (!$this->validate($data, $options)) {
            return;
        }

        $expressions = [];

        foreach ($options['fields'] as $field) {
            if (($expression = $this->process($field, $data, $options)) !== null) {
                $expressions[] = $expression;
            }
        }

        if (empty($expressions)) {
            return;
        }

        $builder = $options['builder'];

        $expression = $options['fields_condition'] === self::CONDITION_OR
            ? $builder->getExpressionBuilder()->orX($expressions)
            : $builder->getExpressionBuilder()->andX($expressions);

        if ($options['builder_condition'] === self::CONDITION_OR) {
            $builder->orWhere($expression);
        } else {
            $builder->andWhere($expression);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'builder_condition' => self::CONDITION_AND,
                'fields_condition'  => self::CONDITION_OR,
                'fields'            => function (Options $options, $fields) {
                    return $fields ?: [$options['filter']->getName()];
                },
            ])
            ->setAllowedValues('builder_condition', $conditions = [self::CONDITION_AND, self::CONDITION_OR])
            ->setAllowedValues('fields_condition', $conditions)
            ->setAllowedTypes('fields', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'filter';
    }

    /**
     * @param string  $field
     * @param mixed   $data
     * @param mixed[] $options
     *
     * @return string|null
     */
    abstract protected function process($field, $data, array $options);

    /**
     * @param mixed   $data
     * @param mixed[] $options
     *
     * @return bool
     */
    protected function validate($data, array $options)
    {
        return $data !== null;
    }
}
