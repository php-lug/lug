<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form;

use Lug\Bundle\ResourceBundle\Form\FormFactory;
use Lug\Bundle\ResourceBundle\Form\FormFactoryInterface as BundleFormFactoryInterface;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface as SymfonyFormFactoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SymfonyFormFactoryInterface
     */
    private $symfonyFormFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->symfonyFormFactory = $this->createSymfonyFormFactoryMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->formFactory = new FormFactory($this->symfonyFormFactory, $this->parameterResolver);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BundleFormFactoryInterface::class, $this->formFactory);
    }

    public function testCreateWithStringType()
    {
        $this->symfonyFormFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($type = 'type'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($options = ['option'])
            )
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->formFactory->create($type, $data, $options));
    }

    public function testCreateWithResourceType()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveForm')
            ->with($this->identicalTo($type = $this->createResourceMock()))
            ->will($this->returnValue($resourceType = 'resource_type'));

        $this->symfonyFormFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($resourceType),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($options = ['option'])
            )
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->formFactory->create($type, $data, $options));
    }

    public function testCreateWithValidationGroups()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveValidationGroups')
            ->will($this->returnValue($groups = ['group']));

        $this->symfonyFormFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($type = 'type'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo(array_merge($options = ['option'], ['validation_groups' => $groups]))
            )
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->formFactory->create($type, $data, $options));
    }

    public function testCreateWithTranslationDomain()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveTranslationDomain')
            ->will($this->returnValue($translationDomain = 'translation_domain'));

        $this->symfonyFormFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($type = 'type'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo(array_merge($options = ['option'], ['translation_domain' => $translationDomain]))
            )
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->formFactory->create($type, $data, $options));
    }

    public function testCreateWithApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->symfonyFormFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($type = 'type'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo(array_merge($options = ['option'], ['csrf_protection' => false]))
            )
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->formFactory->create($type, $data, $options));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SymfonyFormFactoryInterface
     */
    private function createSymfonyFormFactoryMock()
    {
        return $this->createMock(SymfonyFormFactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
