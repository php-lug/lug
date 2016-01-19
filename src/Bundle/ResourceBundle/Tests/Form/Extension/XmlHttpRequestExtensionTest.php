<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form\Extension;

use Lug\Bundle\ResourceBundle\Form\Extension\XmlHttpRequestExtension;
use Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber\Mock\XmlHttpRequestSubscriberMock;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class XmlHttpRequestExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var XmlHttpRequestExtension
     */
    private $extension;

    /**
     * @var XmlHttpRequestSubscriberMock
     */
    private $subscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->subscriber = $this->createXmlHttpRequestSubscriberMock();
        $this->extension = new XmlHttpRequestExtension($this->subscriber);

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(FormType::class, $this->extension->getExtendedType());
    }

    public function testXmlHttpRequestDisabled()
    {
        $form = $this->factory
            ->create(FormType::class)
            ->submit([]);

        $view = $form->createView();

        $this->assertEmpty($form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SUBMIT));

        $this->assertFalse($form->has('_xml_http_request'));

        $this->assertArrayHasKey('xml_http_request', $view->vars);
        $this->assertFalse($view->vars['xml_http_request']);
    }

    public function testXmlHttpRequestEnabled()
    {
        $form = $this->factory
            ->create(FormType::class, null, ['xml_http_request' => true])
            ->submit(['_xml_http_request' => 'true']);

        $view = $form->createView();

        $this->assertSame(
            [[$this->subscriber, 'onPostSubmit']],
            $form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SUBMIT)
        );

        $this->assertTrue($form->has('_xml_http_request'));

        $this->assertArrayHasKey('xml_http_request', $view->vars);
        $this->assertTrue($view->vars['xml_http_request']);

        $this->assertArrayHasKey('_xml_http_request', $view->children);
        $this->assertTrue($view->children['_xml_http_request']->isRendered());
    }

    public function testXmlHttpRequestTriggerDisabled()
    {
        $form = $this->factory
            ->create(FormType::class)
            ->submit([]);

        $view = $form->createView();

        $this->assertArrayNotHasKey('xml_http_request_trigger', $view->vars);
    }

    public function testXmlHttpRequestTriggerEnabled()
    {
        $form = $this->factory
            ->create(FormType::class, null, ['xml_http_request_trigger' => true])
            ->submit([]);

        $view = $form->createView();

        $this->assertArrayHasKey('attr', $view->vars);
        $this->assertInternalType('array', $view->vars['attr']);
        $this->assertArrayHasKey('data-xml-http-request-trigger', $view->vars['attr']);
        $this->assertSame('true', $view->vars['attr']['data-xml-http-request-trigger']);
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidXmlHttpRequest()
    {
        $this->factory->create(FormType::class, null, ['xml_http_request' => 'foo']);
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidXmlHttpRequestTrigger()
    {
        $this->factory->create(FormType::class, null, ['xml_http_request_trigger' => 'foo']);
    }

    /**
     * @return XmlHttpRequestSubscriberMock
     */
    private function createXmlHttpRequestSubscriberMock()
    {
        return new XmlHttpRequestSubscriberMock();
    }
}
