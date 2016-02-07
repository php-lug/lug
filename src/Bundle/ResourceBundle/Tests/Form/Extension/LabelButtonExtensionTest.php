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

use Lug\Bundle\ResourceBundle\Form\Extension\LabelButtonExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\LabelFormExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LabelButtonExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var LabelButtonExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new LabelButtonExtension();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension(new LabelFormExtension())
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(ButtonType::class, $this->extension->getExtendedType());
    }

    public function testDefault()
    {
        $form = $this->factory
            ->createBuilder(FormType::class)
            ->add($buttonName = 'button', ButtonType::class)
            ->getForm();

        $view = $form->createView();
        $buttonView = $view->children[$buttonName];

        $this->assertArrayHasKey('label', $buttonView->vars);
        $this->assertNull($buttonView->vars['label']);

        $this->assertArrayHasKey('label_translation_arguments', $buttonView->vars);
        $this->assertEmpty($buttonView->vars['label_translation_arguments']);
    }

    public function testLabelPrefix()
    {
        $form = $this->factory
            ->createBuilder(FormType::class, null, ['label_prefix' => $labelPrefix = 'prefix'])
            ->add($buttonName = 'button', ButtonType::class)
            ->getForm();

        $view = $form->createView();
        $buttonView = $view->children[$buttonName];

        $this->assertArrayHasKey('label', $buttonView->vars);
        $this->assertSame($labelPrefix.'.'.$buttonName, $buttonView->vars['label']);
    }

    public function testLabelTranslationArguments()
    {
        $form = $this->factory->create(ButtonType::class, null, [
            'label_translation_arguments' => $arguments = ['foo' => 'bar'],
        ]);

        $view = $form->createView();

        $this->assertArrayHasKey('label_translation_arguments', $view->vars);
        $this->assertSame($arguments, $view->vars['label_translation_arguments']);
    }
}
