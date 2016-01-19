<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Batch;

use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface BatcherInterface
{
    /**
     * @param GridInterface $grid
     * @param FormInterface $form
     */
    public function batch(GridInterface $grid, FormInterface $form);
}
