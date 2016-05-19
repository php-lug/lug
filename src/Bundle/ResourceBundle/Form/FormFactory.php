<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface as SymfonyFormFactoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormFactory implements FormFactoryInterface
{
    /**
     * @var SymfonyFormFactoryInterface
     */
    private $factory;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param SymfonyFormFactoryInterface $factory
     * @param ParameterResolverInterface  $parameterResolver
     */
    public function __construct(SymfonyFormFactoryInterface $factory, ParameterResolverInterface $parameterResolver)
    {
        $this->factory = $factory;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ResourceInterface $resource, $type = null, $data = null, array $options = [])
    {
        if ($type === null) {
            $type = $this->parameterResolver->resolveForm($resource);
        }

        $validationGroups = $this->parameterResolver->resolveValidationGroups($resource);
        $translationDomain = $this->parameterResolver->resolveTranslationDomain();

        if (!empty($validationGroups)) {
            $options['validation_groups'] = $validationGroups;
        }

        if (!empty($translationDomain)) {
            $options['translation_domain'] = $translationDomain;
        }

        if ($this->parameterResolver->resolveApi()) {
            $options['csrf_protection'] = false;
        }

        return $this->factory->create($type, $data, $options);
    }
}
