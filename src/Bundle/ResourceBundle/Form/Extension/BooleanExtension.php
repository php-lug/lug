<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form\Extension;

use FOS\RestBundle\Form\Extension\BooleanExtension as FOSBooleanExtension;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanExtension extends FOSBooleanExtension
{
    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(ParameterResolverInterface $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('type', $this->parameterResolver->resolveApi() ? self::TYPE_API : self::TYPE_CHECKBOX);
    }
}
