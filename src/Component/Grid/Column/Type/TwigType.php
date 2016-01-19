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

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TwigType extends AbstractType
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param \Twig_Environment         $twig
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, \Twig_Environment $twig)
    {
        parent::__construct($propertyAccessor);

        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        return $this->renderTemplate($this->getValue($data, $options), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['template'])
            ->setDefault('context', [])
            ->setAllowedTypes('template', 'string')
            ->setAllowedTypes('context', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig';
    }

    /**
     * @param mixed   $data
     * @param mixed[] $options
     *
     * @return string
     */
    protected function renderTemplate($data, array $options)
    {
        return $this->twig->render(
            $options['template'],
            array_merge($options['context'], ['column' => $options['column'], 'data' => $data])
        );
    }
}
