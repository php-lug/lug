<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Form\Type;

use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType as LocaleForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleCodeType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(RepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $locales = array_flip(array_map(function (LocaleInterface $locale) {
            return (string) $locale->getCode();
        }, $this->localeRepository->findAll()));

        foreach ($view->vars['choices'] as $index => $choiceView) {
            if ($view->vars['data'] !== $choiceView->data && isset($locales[$choiceView->data])) {
                unset($view->vars['choices'][$index]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder'     => '',
            'invalid_message' => 'lug.locale.code.invalid',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return LocaleForm::class;
    }
}
