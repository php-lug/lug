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

use Lug\Bundle\ResourceBundle\Form\DataTransformer\BooleanTransformer;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanExtension extends AbstractTypeExtension
{
    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var BooleanTransformer
     */
    private $booleanTransformer;

    /**
     * @param ParameterResolverInterface $parameterResolver
     * @param BooleanTransformer         $booleanTransformer
     */
    public function __construct(ParameterResolverInterface $parameterResolver, BooleanTransformer $booleanTransformer)
    {
        $this->parameterResolver = $parameterResolver;
        $this->booleanTransformer = $booleanTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['api']) {
            $builder
                ->resetViewTransformers()
                ->addViewTransformer($this->booleanTransformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('api', $this->parameterResolver->resolveApi())
            ->setAllowedTypes('api', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CheckboxType::class;
    }
}
