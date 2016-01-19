<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\View;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\View\GridView as BaseGridView;
use Symfony\Component\Form\FormView;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridView extends BaseGridView implements GridViewInterface
{
    /**
     * @var FormView|null
     */
    private $form;

    /**
     * @var FormView|null
     */
    private $batchForm;

    /**
     * @param GridInterface              $definition
     * @param DataSourceBuilderInterface $dataSourceBuilder
     * @param FormView                   $form
     * @param FormView                   $batchForm
     */
    public function __construct(
        GridInterface $definition,
        DataSourceBuilderInterface $dataSourceBuilder,
        FormView $form = null,
        FormView $batchForm = null
    ) {
        parent::__construct($definition, $dataSourceBuilder);

        $this->setForm($form);
        $this->setBatchForm($batchForm);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function setForm(FormView $form = null)
    {
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchForm()
    {
        return $this->batchForm;
    }

    /**
     * {@inheritdoc}
     */
    public function setBatchForm(FormView $batchForm = null)
    {
        $this->batchForm = $batchForm;
    }
}
