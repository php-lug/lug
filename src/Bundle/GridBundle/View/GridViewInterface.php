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

use Lug\Component\Grid\View\GridViewInterface as BaseGridViewInterface;
use Symfony\Component\Form\FormView;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface GridViewInterface extends BaseGridViewInterface
{
    /**
     * @return FormView|null
     */
    public function getForm();

    /**
     * @param FormView|null $form
     */
    public function setForm(FormView $form = null);

    /**
     * @return FormView|null
     */
    public function getBatchForm();

    /**
     * @param FormView|null $batchForm
     */
    public function setBatchForm(FormView $batchForm = null);
}
