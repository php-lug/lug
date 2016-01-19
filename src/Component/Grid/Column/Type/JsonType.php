<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column\Type;

use Lug\Component\Grid\Exception\InvalidTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class JsonType extends AbstractType
{
    /**
     * @var int
     */
    private $options;

    /**
     * @var int
     */
    private $depth;

    /**
     * JsonType constructor.
     *
     * @param PropertyAccessorInterface $propertyAccessor
     * @param int                       $options
     * @param int                       $depth
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, $options = 0, $depth = 512)
    {
        parent::__construct($propertyAccessor);

        $this->options = $options;
        $this->depth = $depth;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        $json = $this->getValue($data, $options);

        if ($json === null) {
            return;
        }

        if (is_resource($json)) {
            throw new InvalidTypeException(sprintf(
                'The "%s" %s column type expects anything except a resource.',
                $options['column']->getName(),
                $this->getName()
            ));
        }

        return json_encode($json, $options['options'], $options['depth']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'options' => $this->options,
                'depth'   => $this->depth,
            ])
            ->setAllowedTypes('options', 'integer')
            ->setAllowedTypes('depth', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'json';
    }
}
