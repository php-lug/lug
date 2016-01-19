<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\Form\Type;

use A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Locale\DefaultProvider;
use A2lix\TranslationFormBundle\TranslationForm\TranslationForm;
use Doctrine\Common\Persistence\ManagerRegistry;
use Lug\Bundle\TranslationBundle\Form\Type\AbstractTranslatableType;
use Lug\Bundle\TranslationBundle\Form\Type\TranslatableType;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Form\Type\ResourceType;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslatableTrait;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\Forms;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TranslatableType
     */
    private $translatableType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $translatableResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $translatableFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->translatableType = new TranslatableType();
        $this->translatableResource = $this->createResourceMock();
        $this->translatableFactory = $this->createFactoryMock();

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addType(new ResourceType())
            ->addType($this->translatableType)
            ->addType(new TranslationsFormsType(
                new TranslationForm($this->createFormRegistryMock(), $this->createManagerRegistryMock()),
                new TranslationsFormsListener(),
                new DefaultProvider(['en', 'fr'], 'en', ['en'])
            ))
            ->addType(new TranslationTestType())
            ->addType(new TranslatableTestType($this->translatableResource, $this->translatableFactory))
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->translatableType);
        $this->assertSame(ResourceType::class, $this->translatableType->getParent());
    }

    public function testSubmit()
    {
        $this->translatableResource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue(TranslatableTest::class));

        $this->translatableResource
            ->expects($this->once())
            ->method('getTranslation')
            ->will($this->returnValue($translationResource = $this->createResourceMock()));

        $translationResource
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue(TranslationTestType::class));

        $this->translatableFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new TranslatableTest()));

        $form = $this->formFactory
            ->create(TranslatableTestType::class)
            ->submit(['translations' => ['en' => ['value' => 'value-en'], 'fr' => ['value' => 'value-fr']]]);

        $translatable = $form->getData();
        $view = $form->createView();

        $this->assertInstanceOf(TranslatableTest::class, $translatable);

        $this->assertCount(1, $view->children);
        $this->assertArrayHasKey('translations', $view->children);

        $this->assertCount(2, $translatable->getTranslations());
        $this->assertTrue($translatable->getTranslations()->containsKey('en'));
        $this->assertTrue($translatable->getTranslations()->containsKey('fr'));

        $this->assertCount(2, $view->children['translations']);
        $this->assertArrayHasKey('en', $view->children['translations']);
        $this->assertArrayHasKey('fr', $view->children['translations']);

        $this->assertInstanceOf(TranslationTest::class, $translatable->getTranslations()['en']);
        $this->assertSame('value-en', $translatable->getTranslations()['en']->getValue());

        $this->assertCount(1, $view->children['translations']['en']);
        $this->assertArrayHasKey('value', $view->children['translations']['en']);

        $this->assertInstanceOf(TranslationTest::class, $translatable->getTranslations()['fr']);
        $this->assertSame('value-fr', $translatable->getTranslations()['fr']->getValue());

        $this->assertCount(1, $view->children['translations']['fr']);
        $this->assertArrayHasKey('value', $view->children['translations']['fr']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormRegistry
     */
    private function createFormRegistryMock()
    {
        return $this->getMockBuilder(FormRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    private function createManagerRegistryMock()
    {
        return $this->getMock(ManagerRegistry::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private function createFactoryMock()
    {
        return $this->getMock(FactoryInterface::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTest implements TranslatableInterface
{
    use TranslatableTrait;

    public function __construct()
    {
        $this->initTranslatable();
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationTest implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTestType extends AbstractTranslatableType
{
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationTestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', TranslationTest::class);
    }
}
