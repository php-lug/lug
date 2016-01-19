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

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLabelExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($view->vars['label'] !== null) {
            return;
        }

        $view->vars['label'] = $label = $this->createLabelPrefix($view, $form);

        if (array_key_exists('label_add', $view->vars) && $view->vars['label_add'] === null) {
            $view->vars['label_add'] = $label.'.add';
            $view->vars['label'] = $label.'.label';
        }

        if (array_key_exists('label_delete', $view->vars) && $view->vars['label_delete'] === null) {
            $view->vars['label_delete'] = $label.'.delete';
            $view->vars['label'] = $label.'.label';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('label_prefix', null);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param int           $depth
     *
     * @return string|null
     */
    private function createLabelPrefix(FormView $view, FormInterface $form, $depth = 1)
    {
        if (($labelPrefix = $form->getConfig()->getOption('label_prefix')) === null) {
            return $this->createParentLabelPrefix($view, $form, $depth);
        }

        if ($depth === 1 && ($rootLabelPrefix = $this->createParentLabelPrefix($view, $form, $depth)) !== null) {
            return $rootLabelPrefix;
        }

        return $labelPrefix;
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param int           $depth
     *
     * @return string|null
     */
    private function createParentLabelPrefix(FormView $view, FormInterface $form, $depth)
    {
        if ($view->parent !== null
            && $form->getParent() !== null
            && ($label = $this->createLabelPrefix($view->parent, $form->getParent(), ++$depth)) !== null) {
            return $label.'.'.$view->vars['name'];
        }
    }
}
