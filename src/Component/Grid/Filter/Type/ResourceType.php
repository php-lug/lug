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

use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceType extends AbstractType
{
    const TYPE_EQUALS = 'equals';
    const TYPE_NOT_EQUALS = 'not_equals';
    const TYPE_EMPTY = 'empty';
    const TYPE_NOT_EMPTY = 'not_empty';

    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

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
            self::TYPE_EQUALS,
            self::TYPE_NOT_EQUALS,
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
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('resource')
            ->setDefault('path', null)
            ->setAllowedTypes('resource', ['string', ResourceInterface::class])
            ->setAllowedTypes('path', ['string', 'null'])
            ->setNormalizer('resource', function (Options $options, $resource) {
                return is_string($resource) ? $this->resourceRegistry[$resource] : $resource;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'resource';
    }

    /**
     * {@inheritdoc}
     */
    protected function process($field, $data, array $options)
    {
        $builder = $options['builder'];
        $alias = $builder->getAliases()[0];
        $prefix = 'lug_'.str_replace('.', '', uniqid(null, true));
        $paths = explode('.', isset($options['path']) ? $options['path'] : $field);
        $field = array_pop($paths);

        foreach ($paths as $index => $path) {
            $builder->innerJoin($alias.'.'.$path, $alias = $prefix.$index);
        }

        switch ($data['type']) {
            case self::TYPE_NOT_EQUALS:
                return $builder->getExpressionBuilder()->neq(
                    $builder->getProperty($field, $alias),
                    $builder->createPlaceholder($field, $data['value'])
                );

            case self::TYPE_EMPTY:
                return $builder->getExpressionBuilder()->isNull($builder->getProperty($field, $alias));

            case self::TYPE_NOT_EMPTY:
                return $builder->getExpressionBuilder()->isNotNull($builder->getProperty($field, $alias));
        }

        return $builder->getExpressionBuilder()->eq(
            $builder->getProperty($field, $alias),
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
                && is_a($data['value'], $options['resource']->getModel())
            ) || in_array($data['type'], self::getEmptyTypes(), true));
    }
}
