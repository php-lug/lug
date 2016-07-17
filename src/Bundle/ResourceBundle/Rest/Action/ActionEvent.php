<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Rest\Action;

use FOS\RestBundle\View\View;
use Lug\Bundle\ResourceBundle\Rest\AbstractEvent;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionEvent extends AbstractEvent
{
    /**
     * @var FormInterface|null
     */
    private $form;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var View|null
     */
    private $view;

    /**
     * @param ResourceInterface  $resource
     * @param string             $action
     * @param FormInterface|null $form
     * @param int                $statusCode
     */
    public function __construct(
        ResourceInterface $resource,
        $action,
        FormInterface $form = null,
        $statusCode = Response::HTTP_NO_CONTENT
    ) {
        parent::__construct($resource, $action);

        $this->form = $form;
        $this->statusCode = $statusCode;
    }

    /**
     * @return FormInterface|null
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }
}
