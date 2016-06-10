<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\EventListener;

use Lug\Bundle\ResourceBundle\EventListener\FlashListener;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Domain\DomainEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FlashListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlashListener
     */
    private $flashListener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->session = $this->createSessionMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->flashListener = new FlashListener($this->session, $this->parameterResolver);
    }

    public function testFlash()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $event = $this->createDomainEventMock();
        $event
            ->expects($this->once())
            ->method('getMessageType')
            ->will($this->returnValue($messageType = 'type'));

        $event
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($message = 'message'));

        $this->session
            ->expects($this->once())
            ->method('getFlashBag')
            ->will($this->returnValue($flashBag = $this->createFlashBagMock()));

        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($messageType), $this->identicalTo($message));

        $this->flashListener->addFlash($event);
    }

    public function testFlashWithApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->session
            ->expects($this->never())
            ->method('getFlashBag');

        $this->flashListener->addFlash($this->createDomainEventMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private function createSessionMock()
    {
        return $this->createMock(Session::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainEvent
     */
    private function createDomainEventMock()
    {
        return $this->createMock(DomainEvent::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FlashBagInterface
     */
    private function createFlashBagMock()
    {
        return $this->createMock(FlashBagInterface::class);
    }
}
