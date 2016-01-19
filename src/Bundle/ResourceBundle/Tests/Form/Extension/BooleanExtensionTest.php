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

use Lug\Bundle\ResourceBundle\Form\Extension\BooleanExtension;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

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
     * @var BooleanExtension
     */
    private $extension;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->extension = new BooleanExtension($this->parameterResolver);

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testType()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $form = $this->formFactory->create(CheckboxType::class);

        $this->assertSame(BooleanExtension::TYPE_API, $form->getConfig()->getOption('type'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }
}
