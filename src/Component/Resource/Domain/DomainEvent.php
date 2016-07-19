<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Domain;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainEvent extends Event
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
     * @var mixed
     */
    private $data;

    /**
     * @var int|null
     */
    private $statusCode;

    /**
     * @var string|null
     */
    private $messageType;

    /**
     * @var string|null
     */
    private $message;

    /**
     * @var bool
     */
    private $stopped = false;

    /**
     * @param ResourceInterface $resource
     * @param string            $action
     * @param mixed             $data
     */
    public function __construct(ResourceInterface $resource, $action, $data = null)
    {
        $this->resource = $resource;
        $this->action = $action;
        $this->data = $data;
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

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->stopped;
    }

    /**
     * @param bool $stopped
     */
    public function setStopped($stopped)
    {
        $this->stopped = $stopped;
    }
}
