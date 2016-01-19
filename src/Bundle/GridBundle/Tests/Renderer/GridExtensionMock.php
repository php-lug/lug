<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Renderer;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridExtensionMock extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = ['is_safe' => ['html']];

        return [
            new \Twig_SimpleFunction('form', [$this, 'form'], $options),
            new \Twig_SimpleFunction('form_start', [$this, 'formStart'], $options),
            new \Twig_SimpleFunction('form_errors', [$this, 'formErrors'], $options),
            new \Twig_SimpleFunction('form_widget', [$this, 'formWidget'], $options),
            new \Twig_SimpleFunction('form_rest', [$this, 'formRest'], $options),
            new \Twig_SimpleFunction('form_end', [$this, 'formEnd'], $options),
            new \Twig_SimpleFunction('path', [$this, 'path'], $options),
            new \Twig_SimpleFunction('pagerfanta', [$this, 'pagerfanta'], $options),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('trans', [$this, 'trans'], ['is_safe' => ['html']])];
    }

    /**
     * @param FormView $view
     * @param mixed[]  $options
     *
     * @return string
     */
    public function form(FormView $view, array $options = [])
    {
        return '<form id="'.$view->vars['id'].'" options="'.json_encode($options).'" />';
    }

    /**
     * @param FormView $view
     * @param mixed[]  $options
     *
     * @return string
     */
    public function formStart(FormView $view, array $options = [])
    {
        return '<form-start id="'.$view->vars['id'].'" options="'.json_encode($options).'" />';
    }

    /**
     * @param FormView $view
     * @param mixed[]  $options
     *
     * @return string
     */
    public function formErrors(FormView $view, array $options = [])
    {
        return '<form-errors id="'.$view->vars['id'].'" options="'.json_encode($options).'" />';
    }

    /**
     * @param FormView $view
     * @param mixed[]  $options
     *
     * @return string
     */
    public function formWidget(FormView $view, array $options = [])
    {
        return '<form-widget id="'.$view->vars['id'].'" options="'.json_encode($options).'" />';
    }

    /**
     * @param FormView $view
     * @param mixed[]  $options
     *
     * @return string
     */
    public function formRest(FormView $view, array $options = [])
    {
        return '<form-rest id="'.$view->vars['id'].'" options="'.json_encode($options).'" />';
    }

    /**
     * @param FormView $view
     * @param mixed[]  $options
     *
     * @return string
     */
    public function formEnd(FormView $view, array $options = [])
    {
        return '<form-end id="'.$view->vars['id'].'" options="'.json_encode($options).'" />';
    }

    /**
     * @param string  $name
     * @param mixed[] $parameters
     * @param int     $referenceType
     *
     * @return string
     */
    public function path($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $name.'_url_'.json_encode($parameters).'_'.$referenceType;
    }

    /**
     * @param Pagerfanta $dataSource
     * @param int        $page
     * @param mixed[]    $options
     *
     * @return string
     */
    public function pagerfanta(Pagerfanta $dataSource, $page, array $options = [])
    {
        return 'pagerfanta_'.$page.'_'.json_encode($options);
    }

    /**
     * @param string      $trans
     * @param mixed[]     $parameters
     * @param string|null $transDomain
     * @param string|null $locale
     *
     * @return string
     */
    public function trans($trans, array $parameters = [], $transDomain = null, $locale = null)
    {
        return $trans.'_trans_'.json_encode($parameters).'_'.$transDomain.'_'.$locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lug_grid_mock';
    }
}
