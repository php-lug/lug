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

use Lug\Bundle\ResourceBundle\Form\EventSubscriber\XmlHttpRequestSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class XmlHttpRequestSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XmlHttpRequestSubscriber
     */
    private $subscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->subscriber = new XmlHttpRequestSubscriber();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [FormEvents::POST_SUBMIT => ['onPostSubmit', 900]],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testXmlHttpRequestDisabled()
    {
        $event = $this->createFormEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_xml_http_request'))
            ->will($this->returnValue($xmlHttpRequestForm = $this->createFormMock()));

        $xmlHttpRequestForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(null));

        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $this->subscriber->onPostSubmit($event);
    }

    public function testXmlHttpRequestEnabled()
    {
        $event = $this->createFormEventMock();
        $event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_xml_http_request'))
            ->will($this->returnValue($xmlHttpRequestForm = $this->createFormMock()));

        $xmlHttpRequestForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('true'));

        $xmlHttpRequestForm
            ->expects($this->once())
            ->method('addError')
            ->with($this->callback(function (FormError $error) {
                return $error->getMessage() === 'The validation has been disabled.';
            }));

        $event
            ->expects($this->once())
            ->method('stopPropagation');

        $this->subscriber->onPostSubmit($event);
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
}
