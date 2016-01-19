<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Action\Type;

use Lug\Component\Grid\Action\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinkType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param UrlGeneratorInterface     $urlGenerator
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, PropertyAccessorInterface $propertyAccessor)
    {
        $this->urlGenerator = $urlGenerator;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        return $this->urlGenerator->generate(
            $options['route'],
            $this->resolveRouteParameters($options['route_parameters'], $data),
            $options['route_reference_type']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'route_parameters'     => [],
                'route_reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,
            ])
            ->setRequired('route')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_parameters', 'array')
            ->setAllowedValues('route_reference_type', [
                UrlGeneratorInterface::ABSOLUTE_PATH,
                UrlGeneratorInterface::ABSOLUTE_URL,
                UrlGeneratorInterface::NETWORK_PATH,
                UrlGeneratorInterface::RELATIVE_PATH,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'link';
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    private function resolveRouteParameters(array $parameters, $data)
    {
        $routeParameters = [];

        foreach ($parameters as $parameter) {
            $routeParameters[$parameter] = $this->propertyAccessor->getValue($data, $parameter);
        }

        return $routeParameters;
    }
}
