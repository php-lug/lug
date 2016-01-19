<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form\Type;

use Lug\Bundle\ResourceBundle\Form\Type\CsrfProtectionType;
use Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber\Mock\FlashCsrfProtectionSubscriberMock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CsrfProtectionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var CsrfProtectionType
     */
    private $csrfProtectionType;

    /**
     * @var FlashCsrfProtectionSubscriberMock
     */
    private $flashCsrfProtectionSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->flashCsrfProtectionSubscriber = $this->createFlashCsrfProtectionSubscriberMock();
        $this->csrfProtectionType = new CsrfProtectionType($this->flashCsrfProtectionSubscriber);

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->csrfProtectionType)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->csrfProtectionType);
    }

    public function testIdUnicity()
    {
        $form1 = $this->factory
            ->create(CsrfProtectionType::class)
            ->createView();

        $form2 = $this->factory
            ->create(CsrfProtectionType::class)
            ->createView();

        $this->assertNotSame($form1->vars['id'], $form2->vars['id']);
    }

    public function testSubmit()
    {
        $form = $this->factory
            ->create(CsrfProtectionType::class)
            ->submit([]);

        $view = $form->createView();

        $this->assertArrayHasKey('submit', $view->children);
        $this->assertArrayHasKey('clicked', $view->children['submit']->vars);

        $this->assertSame(
            [[$this->flashCsrfProtectionSubscriber, 'onPostSubmit']],
            $form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SUBMIT)
        );
    }

    /**
     * @return FlashCsrfProtectionSubscriberMock
     */
    private function createFlashCsrfProtectionSubscriberMock()
    {
        return new FlashCsrfProtectionSubscriberMock();
    }
}
