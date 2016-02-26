<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Form\Type;

use Lug\Component\Locale\Form\Type\LocaleCodeType;
use Lug\Component\Locale\Form\Type\LocaleType;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Form\Type\AbstractResourceType;
use Lug\Component\Resource\Form\Type\ResourceType;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var LocaleType
     */
    private $localeType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $resourceFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->resourceFactory = $this->createFactoryMock();
        $this->repository = $this->createRepositoryMock();
        $this->localeProvider = $this->createLocaleProviderMock();

        $this->resource
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('locale'));

        $this->localeType = new LocaleType($this->resource, $this->resourceFactory, $this->localeProvider);

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addType(new ResourceType())
            ->addType($this->localeType)
            ->addType(new LocaleCodeType($this->repository))
            ->addExtension(new ValidatorExtension((new ValidatorBuilder())->getValidator()))
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractResourceType::class, $this->localeType);
    }

    public function testSubmit()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($this->createLocaleMock()));

        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue(get_class($locale = $this->createLocaleMock())));

        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([]));

        $this->resourceFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($locale));

        $locale
            ->expects($this->once())
            ->method('setCode')
            ->with($this->identicalTo($code = 'fr'));

        $locale
            ->expects($this->once())
            ->method('setEnabled')
            ->with($this->identicalTo(true));

        $locale
            ->expects($this->once())
            ->method('setRequired')
            ->with($this->identicalTo(true));

        $form = $this->formFactory
            ->create(LocaleType::class)
            ->submit(['code' => $code, 'enabled' => true, 'required' => true]);

        $view = $form->createView();

        $this->assertCount(4, $view->children);
        $this->assertArrayHasKey('code', $view->children);
        $this->assertArrayHasKey('enabled', $view->children);
        $this->assertArrayHasKey('required', $view->children);
        $this->assertArrayHasKey('submit', $view->children);

        $this->assertArrayHasKey('disabled', $view->children['code']->vars);
        $this->assertFalse($view->children['code']->vars['disabled']);

        $this->assertArrayHasKey('disabled', $view->children['enabled']->vars);
        $this->assertFalse($view->children['enabled']->vars['disabled']);

        $this->assertArrayHasKey('disabled', $view->children['required']->vars);
        $this->assertFalse($view->children['required']->vars['disabled']);
    }

    public function testSubmitDefaultLocale()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($defaultLocale = $this->createLocaleMock()));

        $defaultLocale
            ->expects($this->exactly(2))
            ->method('getCode')
            ->will($this->returnValue($defaultLocaleCode = 'fr'));

        $defaultLocale
            ->expects($this->never())
            ->method('setCode');

        $defaultLocale
            ->expects($this->never())
            ->method('setEnabled');

        $defaultLocale
            ->expects($this->never())
            ->method('setRequired');

        $this->resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue(get_class($defaultLocale)));

        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$defaultLocale]));

        $form = $this->formFactory
            ->create(LocaleType::class, $defaultLocale)
            ->submit(['code' => 'USD', 'enabled' => false, 'required' => false]);

        $view = $form->createView();

        $this->assertCount(4, $view->children);
        $this->assertArrayHasKey('code', $view->children);
        $this->assertArrayHasKey('enabled', $view->children);
        $this->assertArrayHasKey('required', $view->children);
        $this->assertArrayHasKey('submit', $view->children);

        $this->assertArrayHasKey('disabled', $view->children['code']->vars);
        $this->assertTrue($view->children['code']->vars['disabled']);

        $this->assertArrayHasKey('disabled', $view->children['enabled']->vars);
        $this->assertTrue($view->children['enabled']->vars['disabled']);

        $this->assertArrayHasKey('disabled', $view->children['required']->vars);
        $this->assertTrue($view->children['required']->vars['disabled']);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->getMock(RepositoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->getMock(LocaleProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->getMock(LocaleInterface::class);
    }
}
