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

use Lug\Component\Grid\Handler\GridHandlerInterface as BaseGridHandlerInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridHandler implements GridHandlerInterface
{
    /**
     * @var BaseGridHandlerInterface
     */
    private $handler;

    /**
     * @param BaseGridHandlerInterface $handler
     */
    public function __construct(BaseGridHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GridInterface $grid, FormInterface $form = null, FormInterface $batchForm = null)
    {
        $valid = $form !== null && $form->isValid();

        $view = $this->handler->handle(
            $grid,
            $valid ? $form->get('filters')->getData() : [],
            $valid ? $form->get('sorting')->getData() : [],
            $valid ? ['page' => $form->get('page')->getData(), 'limit' => $form->get('limit')->getData()] : []
        );

        if ($form !== null) {
            $view->setForm($form->createView());
        }

        if ($batchForm !== null) {
            $view->setBatchForm($batchForm->createView());
        }

        return $view;
    }
}
