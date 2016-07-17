<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Rest\View;

use FOS\RestBundle\View\View;
use Lug\Bundle\ResourceBundle\Rest\AbstractEvent;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ViewEvent extends AbstractEvent
{
    /**
     * @var View
     */
    private $view;

    /**
     * @param ResourceInterface $resource
     * @param string            $action
     * @param View              $view
     */
    public function __construct(ResourceInterface $resource, $action, View $view)
    {
        parent::__construct($resource, $action);

        $this->view = $view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }
}
