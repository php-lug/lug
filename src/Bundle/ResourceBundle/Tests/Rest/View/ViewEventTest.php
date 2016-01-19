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
use Lug\Bundle\ResourceBundle\Rest\View\ViewEvent;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ViewEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewEvent
     */
    private $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|View
     */
    private $view;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->view = $this->createViewMock();

        $this->event = new ViewEvent($this->resource, $this->view);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->event);
    }

    public function testInitialState()
    {
        $this->assertSame($this->resource, $this->event->getResource());
        $this->assertSame($this->view, $this->event->getView());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|View
     */
    private function createViewMock()
    {
        return $this->getMock(View::class);
    }
}
