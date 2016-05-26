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

use Ivory\Base64FileBundle\Form\Extension\Base64FileExtension as IvoryBase64FileExtension;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Base64FileExtension extends IvoryBase64FileExtension
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
        parent::__construct(false);

        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'base64' => $this->parameterResolver->resolveApi(),
        ]);
    }
}
