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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\FormInterface;

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
     * @param string|FormTypeInterface|ResourceInterface $type
     * @param mixed                                      $data
     * @param mixed[]                                    $options
     *
     * @return FormInterface
     */
    public function create($type = null, $data = null, array $options = [])
    {
        if ($type instanceof ResourceInterface) {
            $type = $this->parameterResolver->resolveForm($type);
        }

        $validationGroups = $this->parameterResolver->resolveValidationGroups();
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
