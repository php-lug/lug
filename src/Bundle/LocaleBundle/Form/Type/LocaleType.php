<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Form\Type;

use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Form\Type\AbstractResourceType;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleType extends AbstractResourceType
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param ResourceInterface       $resource
     * @param FactoryInterface        $factory
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(
        ResourceInterface $resource,
        FactoryInterface $factory,
        LocaleProviderInterface $localeProvider
    ) {
        parent::__construct($resource, $factory);

        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $childOptions = ['disabled' => $builder->getData() === $this->localeProvider->getDefaultLocale()];

        $builder
            ->add('code', LocaleCodeType::class, $childOptions)
            ->add('enabled', CheckboxType::class, array_merge(
                ['invalid_message' => 'lug.locale.enabled.invalid'],
                $childOptions
            ))
            ->add('required', CheckboxType::class, array_merge(
                ['invalid_message' => 'lug.locale.required.invalid'],
                $childOptions
            ))
            ->add('submit', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lug_locale';
    }
}
