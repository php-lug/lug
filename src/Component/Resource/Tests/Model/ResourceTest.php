<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Lug\Component\Resource\Domain\DomainManagerInterface;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\Resource;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $driverManager;

    /**
     * @var string
     */
    private $driverMappingPath;

    /**
     * @var string
     */
    private $driverMappingFormat;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $interfaces;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $factory;

    /**
     * @var string
     */
    private $repository;

    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $choiceForm;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $domainManager;

    /**
     * @var string
     */
    private $idPropertyPath = 'id';

    /**
     * @var string
     */
    private $labelPropertyPath = 'label';

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->name = 'name';
        $this->driver = ResourceInterface::DRIVER_DOCTRINE_MONGODB;
        $this->driverManager = 'my.manager';
        $this->driverMappingPath = __DIR__;
        $this->driverMappingFormat = ResourceInterface::DRIVER_MAPPING_FORMAT_XML;
        $this->interfaces = [\ArrayAccess::class, \Countable::class];
        $this->model = \ArrayIterator::class;
        $this->factory = $this->createFactoryClassMock();
        $this->repository = $this->createRepositoryClassMock();
        $this->form = $this->createFormClassMock();
        $this->choiceForm = $this->createFormClassMock();
        $this->controller = $this->createControllerClassMock();
        $this->domainManager = $this->createDomainManagerClassMock();
        $this->idPropertyPath = 'id';
        $this->labelPropertyPath = 'label';

        $this->resource = new Resource(
            $this->name,
            $this->driver,
            $this->driverManager,
            $this->driverMappingPath,
            $this->driverMappingFormat,
            $this->interfaces,
            $this->model,
            $this->controller,
            $this->factory,
            $this->repository,
            $this->domainManager,
            $this->form,
            $this->choiceForm,
            $this->idPropertyPath,
            $this->labelPropertyPath
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->resource);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->resource->getName());
        $this->assertSame($this->driver, $this->resource->getDriver());
        $this->assertSame($this->driverManager, $this->resource->getDriverManager());
        $this->assertSame($this->driverMappingPath, $this->resource->getDriverMappingPath());
        $this->assertSame($this->driverMappingFormat, $this->resource->getDriverMappingFormat());
        $this->assertSame($this->interfaces, $this->resource->getInterfaces());
        $this->assertSame($this->model, $this->resource->getModel());
        $this->assertSame($this->factory, $this->resource->getFactory());
        $this->assertSame($this->repository, $this->resource->getRepository());
        $this->assertSame($this->form, $this->resource->getForm());
        $this->assertSame($this->choiceForm, $this->resource->getChoiceForm());
        $this->assertSame($this->controller, $this->resource->getController());
        $this->assertSame($this->domainManager, $this->resource->getDomainManager());
        $this->assertSame($this->idPropertyPath, $this->resource->getIdPropertyPath());
        $this->assertSame($this->labelPropertyPath, $this->resource->getLabelPropertyPath());
        $this->assertNull($this->resource->getTranslation());
    }

    public function testInitialState()
    {
        $translation = $this->createResourceMock();

        $this->resource = new Resource(
            $this->name,
            $this->driver,
            $this->driverManager,
            $this->driverMappingPath,
            $this->driverMappingFormat,
            $this->interfaces,
            $this->model,
            $this->controller,
            $this->factory,
            $this->repository,
            $this->domainManager,
            $this->form,
            $this->choiceForm,
            $this->idPropertyPath,
            $this->labelPropertyPath,
            $translation
        );

        $this->assertSame($translation, $this->resource->getTranslation());
    }

    public function testDriver()
    {
        $this->resource->setDriver($driver = ResourceInterface::DRIVER_DOCTRINE_ORM);

        $this->assertSame($driver, $this->resource->getDriver());
    }

    public function testDriverManager()
    {
        $this->resource->setDriverManager($driverManager = 'default');

        $this->assertSame($driverManager, $this->resource->getDriverManager());
    }

    public function testDriverMappingPath()
    {
        $this->resource->setDriverMappingPath($driverMappingPath = __DIR__.'/default');

        $this->assertSame($driverMappingPath, $this->resource->getDriverMappingPath());
    }

    public function testDriverMappingFormat()
    {
        $this->resource->setDriverMappingFormat($driverMappingFormat = ResourceInterface::DRIVER_MAPPING_FORMAT_YAML);

        $this->assertSame($driverMappingFormat, $this->resource->getDriverMappingFormat());
    }

    public function testModel()
    {
        $this->resource->setModel($model = ArrayCollection::class);

        $this->assertSame($model, $this->resource->getModel());
    }

    public function testFactory()
    {
        $this->resource->setFactory($factory = $this->createFactoryClassMock());

        $this->assertSame($factory, $this->resource->getFactory());
    }

    public function testRepository()
    {
        $this->resource->setRepository($repository = $this->createRepositoryClassMock());

        $this->assertSame($repository, $this->resource->getRepository());
    }

    public function testForm()
    {
        $this->resource->setForm($form = $this->createFormClassMock());

        $this->assertSame($form, $this->resource->getForm());
    }

    public function testChoiceForm()
    {
        $this->resource->setChoiceForm($choiceForm = $this->createFormClassMock());

        $this->assertSame($choiceForm, $this->resource->getChoiceForm());
    }

    public function testController()
    {
        $this->resource->setController($controller = $this->createControllerClassMock());

        $this->assertSame($controller, $this->resource->getController());
    }

    public function testDomainManager()
    {
        $this->resource->setDomainManager($domainManager = $this->createDomainManagerClassMock());

        $this->assertSame($domainManager, $this->resource->getDomainManager());
    }

    public function testIdPropertyPath()
    {
        $this->resource->setIdPropertyPath($idPropertyPath = 'id');

        $this->assertSame($idPropertyPath, $this->resource->getIdPropertyPath());
    }

    public function testLabelPropertyPath()
    {
        $this->resource->setLabelPropertyPath($labelPropertyPath = 'label');

        $this->assertSame($labelPropertyPath, $this->resource->getLabelPropertyPath());
    }

    /**
     * @return string
     */
    private function createFactoryClassMock()
    {
        return $this->getMockClass(FactoryInterface::class);
    }

    /**
     * @return string
     */
    private function createRepositoryClassMock()
    {
        return $this->getMockClass(RepositoryInterface::class);
    }

    /**
     * @return string
     */
    private function createFormClassMock()
    {
        return $this->getMockClass(FormInterface::class);
    }

    /**
     * @return string
     */
    private function createControllerClassMock()
    {
        return $this->getMockClass(\stdClass::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainManagerInterface
     */
    private function createDomainManagerClassMock()
    {
        return $this->getMock(DomainManagerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
