<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber;

use Lug\Bundle\ResourceBundle\Form\EventSubscriber\FlashCsrfProtectionSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FlashCsrfProtectionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlashCsrfProtectionSubscriber
     */
    private $flashCsrfProtectionSubscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->session = $this->createSessionMock();
        $this->translator = $this->createTranslatorMock();

        $this->flashCsrfProtectionSubscriber = new FlashCsrfProtectionSubscriber($this->session, $this->translator);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->flashCsrfProtectionSubscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [FormEvents::POST_SUBMIT => 'onPostSubmit'],
            $this->flashCsrfProtectionSubscriber->getSubscribedEvents()
        );
    }

    public function testOnPostSubmitWithValidForm()
    {
        $event = $this->createFormEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->session
            ->expects($this->never())
            ->method('getFlashBag');

        $this->translator
            ->expects($this->never())
            ->method('trans');

        $this->flashCsrfProtectionSubscriber->onPostSubmit($event);
    }

    public function testOnPostSubmitWithInvalidForm()
    {
        $event = $this->createFormEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->session
            ->expects($this->once())
            ->method('getFlashBag')
            ->will($this->returnValue($flashBag = $this->createFlashBagMock()));

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with(
                $this->identicalTo('lug.resource.csrf.error'),
                $this->identicalTo([]),
                $this->identicalTo('flashes')
            )
            ->will($this->returnValue($translation = 'translation'));

        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->identicalTo('error'),
                $this->identicalTo($translation)
            );

        $this->flashCsrfProtectionSubscriber->onPostSubmit($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private function createSessionMock()
    {
        return $this->getMock(Session::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private function createTranslatorMock()
    {
        return $this->getMock(TranslatorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormEvent
     */
    private function createFormEventMock()
    {
        return $this->getMockBuilder(FormEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FlashBagInterface
     */
    private function createFlashBagMock()
    {
        return $this->getMockBuilder(FlashBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
