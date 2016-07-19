<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Domain;

use Lug\Component\Resource\Domain\DomainEvent;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainEvent
     */
    private $domainEvent;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var string
     */
    private $action;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->action = 'action';

        $this->domainEvent = new DomainEvent($this->resource, $this->action);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Event::class, $this->domainEvent);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->resource, $this->domainEvent->getResource());
        $this->assertSame($this->action, $this->domainEvent->getAction());
        $this->assertNull($this->domainEvent->getData());
        $this->assertNull($this->domainEvent->getStatusCode());
        $this->assertNull($this->domainEvent->getMessageType());
        $this->assertNull($this->domainEvent->getMessage());
        $this->assertFalse($this->domainEvent->isStopped());
    }

    public function testInitialState()
    {
        $this->domainEvent = new DomainEvent($this->resource, $this->action, $data = new \stdClass());

        $this->assertSame($this->resource, $this->domainEvent->getResource());
        $this->assertSame($this->action, $this->domainEvent->getAction());
        $this->assertSame($data, $this->domainEvent->getData());
        $this->assertNull($this->domainEvent->getStatusCode());
        $this->assertNull($this->domainEvent->getMessageType());
        $this->assertNull($this->domainEvent->getMessage());
        $this->assertFalse($this->domainEvent->isStopped());
    }

    public function testData()
    {
        $this->domainEvent->setData($data = new \stdClass());

        $this->assertSame($data, $this->domainEvent->getData());
    }

    public function testStatusCode()
    {
        $this->domainEvent->setStatusCode($statusCode = Response::HTTP_CREATED);

        $this->assertSame($statusCode, $this->domainEvent->getStatusCode());
    }

    public function testMessageType()
    {
        $this->domainEvent->setMessageType($messageType = 'message_type');

        $this->assertSame($messageType, $this->domainEvent->getMessageType());
    }

    public function testMessage()
    {
        $this->domainEvent->setMessage($message = 'message');

        $this->assertSame($message, $this->domainEvent->getMessage());
    }

    public function testStopped()
    {
        $this->domainEvent->setStopped(true);

        $this->assertTrue($this->domainEvent->isStopped());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
