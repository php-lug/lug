<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Rest;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractEvent extends Event
{
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var string
     */
    private $action;

    /**
     * @param ResourceInterface $resource
     * @param string            $action
     */
    public function __construct(ResourceInterface $resource, $action)
    {
        $this->resource = $resource;
        $this->action = $action;
    }

    /**
     * @return ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
