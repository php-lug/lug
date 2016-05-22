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

use Lug\Bundle\ResourceBundle\Form\DataTransformer\BooleanTransformer;
use Lug\Bundle\ResourceBundle\Form\Extension\BooleanExtension;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var BooleanTransformer
     */
    private $booleanTransformer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->booleanTransformer = new BooleanTransformer();

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addTypeExtension(new BooleanExtension($this->parameterResolver, $this->booleanTransformer))
            ->getFormFactory();
    }

    public function testCheckboxType()
    {
        $viewTransformers = $this->formFactory
            ->create(CheckboxType::class, null, ['api' => false])
            ->getConfig()
            ->getViewTransformers();

        $this->assertCount(1, $viewTransformers);
        $this->assertInstanceOf(BooleanToStringTransformer::class, $viewTransformers[0]);
    }

    public function testApiType()
    {
        $viewTransformers = $this->formFactory
            ->create(CheckboxType::class, null, ['api' => true])
            ->getConfig()
            ->getViewTransformers();

        $this->assertCount(1, $viewTransformers);
        $this->assertInstanceOf(BooleanTransformer::class, $viewTransformers[0]);
    }

    /**
     * @dataProvider validInitialProvider
     */
    public function testValidInitialData($expected, $data)
    {
        $form = $this->formFactory->create(CheckboxType::class, $data, ['api' => true]);

        $this->assertSame($expected, $form->getData());
    }

    /**
     * @dataProvider invalidProvider
     *
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage The boolean type expects a boolean or null value
     */
    public function testInvalidInitialData($data)
    {
        $this->formFactory->create(CheckboxType::class, $data, ['api' => true]);
    }

    /**
     * @dataProvider validSubmitProvider
     */
    public function testValidSubmittedData($expected, $data)
    {
        $form = $this->formFactory
            ->create(CheckboxType::class, null, ['api' => true])
            ->submit($data);

        $this->assertTrue($form->isValid());
        $this->assertSame($expected, $form->getData());
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalidSubmittedData($data)
    {
        $form = $this->formFactory
            ->create(CheckboxType::class, null, ['api' => true])
            ->submit($data);

        $this->assertFalse($form->isValid());
        $this->assertNull($form->getData());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "api" with value "foo" is expected to be of type "bool", but is of type "string".
     */
    public function testInvalidApiOption()
    {
        $this->formFactory->create(CheckboxType::class, null, ['api' => 'foo']);
    }

    /**
     * @return mixed[]
     */
    public function validInitialProvider()
    {
        return [
            [true, true],
            [false, false],
            [false, null],
        ];
    }

    /**
     * @return mixed[]
     */
    public function validSubmitProvider()
    {
        return [
            [true, true],
            [true, 1],
            [true, '1'],
            [true, 'true'],
            [true, 'yes'],
            [true, 'on'],
            [false, false],
            [false, 0],
            [false, '0'],
            [false, 'false'],
            [false, 'no'],
            [false, 'off'],
            [false, ''],
            [false, null],
        ];
    }

    /**
     * @return mixed[]
     */
    public function invalidProvider()
    {
        return [
            ['foo'],
            [1.2],
            [new \stdClass()],
            [['foo' => 'bar']],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }
}
