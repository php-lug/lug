<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Handler;

use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface GridHandlerInterface
{
    /**
     * @param GridInterface      $grid
     * @param FormInterface|null $form
     * @param FormInterface|null $batchForm
     *
     * @return GridViewInterface
     */
    public function handle(GridInterface $grid, FormInterface $form = null, FormInterface $batchForm = null);
}
