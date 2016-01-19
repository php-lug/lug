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

use Lug\Bundle\ResourceBundle\Form\Type\CsrfProtectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinkSecureType extends LinkType
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param UrlGeneratorInterface     $urlGenerator
     * @param PropertyAccessorInterface $propertyAccessor
     * @param FormFactoryInterface      $formFactory
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        PropertyAccessorInterface $propertyAccessor,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($urlGenerator, $propertyAccessor);

        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function render($data, array $options)
    {
        $action = $options['action'];

        return $this->formFactory->create(CsrfProtectionType::class, null, [
            'method'             => strtoupper($options['method']),
            'action'             => parent::render($data, $options),
            'label'              => $action->getLabel(),
            'translation_domain' => $action->getOption('trans_domain'),
        ])->createView();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('method', Request::METHOD_GET)
            ->setAllowedTypes('method', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'link_secure';
    }
}
