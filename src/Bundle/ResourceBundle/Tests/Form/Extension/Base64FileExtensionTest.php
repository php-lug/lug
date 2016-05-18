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

use Lug\Bundle\ResourceBundle\Form\Extension\Base64FileExtension;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Base64FileExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Base64FileExtension
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
        $this->extension = new Base64FileExtension($this->parameterResolver);

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

        $form = $this->formFactory->create(FileType::class);

        $this->assertTrue($form->getConfig()->getOption('base64'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }
}
