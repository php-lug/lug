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

use Lug\Bundle\ResourceBundle\Form\Extension\CollectionExtension;
use Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber\Mock\CollectionSubscriberMock;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CollectionExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var CollectionExtension
     */
    private $extension;

    /**
     * @var CollectionSubscriberMock
     */
    private $subscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->subscriber = $this->createCollectionSubscriberMock();
        $this->extension = new CollectionExtension($this->subscriber);

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(CollectionType::class, $this->extension->getExtendedType());
    }

    public function testDisableReference()
    {
        $form = $this->factory
            ->create(CollectionType::class)
            ->submit([]);

        $form->createView();

        $this->assertFalse($form->getConfig()->getByReference());
    }

    public function testDefault()
    {
        $form = $this->factory
            ->create(CollectionType::class)
            ->submit([]);

        $form->createView();

        $this->assertEmpty($form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SET_DATA));
        $this->assertEmpty($form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SUBMIT));
    }

    public function testAllowDelete()
    {
        $form = $this->factory
            ->create(CollectionType::class, [], ['allow_delete' => true])
            ->submit([]);

        $form->createView();

        $this->assertEquals(
            [[$this->subscriber, 'init']],
            $form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SET_DATA)
        );

        $this->assertEquals(
            [[$this->subscriber, 'manage']],
            $form->getConfig()->getEventDispatcher()->getListeners(FormEvents::POST_SUBMIT)
        );
    }

    public function testLabelDelete()
    {
        $form = $this->factory
            ->create(CollectionType::class, [], [
                'allow_delete' => true,
                'label_delete' => $label = 'foo',
            ])
            ->submit([]);

        $view = $form->createView();

        $this->assertArrayHasKey('label_delete', $view->vars);
        $this->assertSame($label, $view->vars['label_delete']);
    }

    public function testLabelAdd()
    {
        $form = $this->factory
            ->create(CollectionType::class, [], [
                'allow_add' => true,
                'label_add' => $label = 'foo',
            ])
            ->submit([]);

        $view = $form->createView();

        $this->assertArrayHasKey('label_add', $view->vars);
        $this->assertSame($label, $view->vars['label_add']);
    }

    /**
     * @return CollectionSubscriberMock
     */
    private function createCollectionSubscriberMock()
    {
        return new CollectionSubscriberMock();
    }
}
