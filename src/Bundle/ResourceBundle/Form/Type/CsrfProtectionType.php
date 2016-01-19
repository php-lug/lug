<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form\Type;

use Lug\Bundle\ResourceBundle\Form\EventSubscriber\FlashCsrfProtectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CsrfProtectionType extends AbstractType
{
    /**
     * @var FlashCsrfProtectionSubscriber
     */
    private $flashCsrfProtectionSubscriber;

    /**
     * @param FlashCsrfProtectionSubscriber $flashCsrfProtectionSubscriber
     */
    public function __construct(FlashCsrfProtectionSubscriber $flashCsrfProtectionSubscriber)
    {
        $this->flashCsrfProtectionSubscriber = $flashCsrfProtectionSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('submit', SubmitType::class, ['label' => $options['label']])
            ->addEventSubscriber($this->flashCsrfProtectionSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id'] .= '_'.str_replace('.', '', uniqid(null, true));
    }
}
