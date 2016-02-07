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
use Lug\Bundle\ResourceBundle\Form\Extension\LabelFormExtension;
use Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber\Mock\CollectionSubscriberMock;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LabelFormExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var LabelFormExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new LabelFormExtension();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(FormType::class, $this->extension->getExtendedType());
    }

    public function testDefault()
    {
        $form = $this->factory
            ->createBuilder(FormType::class)
            ->add($fieldName = 'field', FormType::class)
            ->getForm();

        $view = $form->createView();
        $buttonView = $view->children[$fieldName];

        $this->assertArrayHasKey('label', $buttonView->vars);
        $this->assertNull($buttonView->vars['label']);

        $this->assertArrayHasKey('label_translation_arguments', $buttonView->vars);
        $this->assertEmpty($buttonView->vars['label_translation_arguments']);
    }

    public function testLabelPrefix()
    {
        $form = $this->factory
            ->createBuilder(FormType::class, null, ['label_prefix' => $labelPrefix = 'prefix'])
            ->add($fieldName = 'field')
            ->getForm();

        $view = $form->createView();
        $fieldView = $view->children[$fieldName];

        $this->assertArrayHasKey('label', $fieldView->vars);
        $this->assertSame($labelPrefix.'.'.$fieldName, $fieldView->vars['label']);
    }

    public function testEmbedLabelPrefix()
    {
        $embedForm = $this->factory
            ->createNamedBuilder(
                $embedFormName = 'embed_form',
                FormType::class,
                null,
                ['label_prefix' => $embedLabelPrefix = 'embed_prefix']
            )
            ->add($embedFieldName = 'embed_field');

        $form = $this->factory
            ->createBuilder(FormType::class, null, ['label_prefix' => $labelPrefix = 'prefix'])
            ->add($embedForm)
            ->add($fieldName = 'field')
            ->getForm();

        $view = $form->createView();

        $fieldView = $view->children[$fieldName];
        $embedFormView = $view->children[$embedFormName];
        $embedFieldView = $embedFormView->children[$embedFieldName];

        $this->assertArrayHasKey('label', $fieldView->vars);
        $this->assertSame($labelPrefix.'.'.$fieldName, $fieldView->vars['label']);

        $this->assertArrayHasKey('label', $embedFormView->vars);
        $this->assertSame($labelPrefix.'.'.$embedFormName, $embedFormView->vars['label']);

        $this->assertArrayHasKey('label', $embedFieldView->vars);
        $this->assertSame($embedLabelPrefix.'.'.$embedFieldName, $embedFieldView->vars['label']);
    }

    public function testLabelPrefixWithExplicitLabel()
    {
        $form = $this->factory
            ->createBuilder(FormType::class, null, ['label_prefix' => 'prefix'])
            ->add($fieldName = 'field', null, ['label' => $explicitLabel = 'explicit'])
            ->getForm();

        $view = $form->createView();
        $fieldView = $view->children[$fieldName];

        $this->assertArrayHasKey('label', $fieldView->vars);
        $this->assertSame($explicitLabel, $fieldView->vars['label']);
    }

    public function testLabelPrefixWithAllowAdd()
    {
        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->addTypeExtension(new CollectionExtension(new CollectionSubscriberMock()))
            ->getFormFactory();

        $form = $this->factory
            ->createBuilder(FormType::class, null, ['label_prefix' => $labelPrefix = 'prefix'])
            ->add($fieldName = 'field', CollectionType::class, ['allow_add' => true])
            ->getForm();

        $view = $form->createView();
        $fieldView = $view->children[$fieldName];

        $this->assertArrayHasKey('label', $fieldView->vars);
        $this->assertSame($labelPrefix.'.'.$fieldName.'.label', $fieldView->vars['label']);

        $this->assertArrayHasKey('label_add', $fieldView->vars);
        $this->assertSame($labelPrefix.'.'.$fieldName.'.add', $fieldView->vars['label_add']);
    }

    public function testLabelPrefixWithAllowDelete()
    {
        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->addTypeExtension(new CollectionExtension(new CollectionSubscriberMock()))
            ->getFormFactory();

        $form = $this->factory
            ->createBuilder(FormType::class, null, ['label_prefix' => $labelPrefix = 'prefix'])
            ->add($fieldName = 'field', CollectionType::class, ['allow_delete' => true])
            ->getForm();

        $view = $form->createView();
        $fieldView = $view->children[$fieldName];

        $this->assertArrayHasKey('label', $fieldView->vars);
        $this->assertSame($labelPrefix.'.'.$fieldName.'.label', $fieldView->vars['label']);

        $this->assertArrayHasKey('label_delete', $fieldView->vars);
        $this->assertSame($labelPrefix.'.'.$fieldName.'.delete', $fieldView->vars['label_delete']);
    }

    public function testLabelTranslationArguments()
    {
        $form = $this->factory->create(FormType::class, null, [
            'label_translation_arguments' => $arguments = ['foo' => 'bar'],
        ]);

        $view = $form->createView();

        $this->assertArrayHasKey('label_translation_arguments', $view->vars);
        $this->assertSame($arguments, $view->vars['label_translation_arguments']);
    }
}
