<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Rest;

use FOS\RestBundle\View\View;
use Lug\Bundle\ResourceBundle\Rest\AbstractEvent;
use Lug\Bundle\ResourceBundle\Rest\Action\ActionEvent;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionEvent
     */
    private $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private $form;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->form = $this->createFormMock();
        $this->statusCode = Response::HTTP_CREATED;

        $this->event = new ActionEvent($this->resource, $this->form, $this->statusCode);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->event);
    }

    public function testDefaultState()
    {
        $this->event = new ActionEvent($this->resource);

        $this->assertSame($this->resource, $this->event->getResource());
        $this->assertNull($this->event->getForm());
        $this->assertSame(Response::HTTP_NO_CONTENT, $this->event->getStatusCode());
        $this->assertNull($this->event->getView());
    }

    public function testInitialState()
    {
        $this->assertSame($this->resource, $this->event->getResource());
        $this->assertSame($this->form, $this->event->getForm());
        $this->assertSame($this->statusCode, $this->event->getStatusCode());
        $this->assertNull($this->event->getView());
    }

    public function testView()
    {
        $this->event->setView($view = $this->createViewMock());

        $this->assertSame($view, $this->event->getView());
        $this->assertTrue($this->event->isPropagationStopped());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->createMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|View
     */
    private function createViewMock()
    {
        return $this->createMock(View::class);
    }
}
