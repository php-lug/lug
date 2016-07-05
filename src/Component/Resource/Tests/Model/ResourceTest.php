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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->name = 'name';
        $this->interfaces = [\ArrayAccess::class, \Countable::class];
        $this->model = \ArrayIterator::class;

        $this->resource = new Resource($this->name, $this->interfaces, $this->model);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->resource);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->resource->getName());
        $this->assertSame($this->interfaces, $this->resource->getInterfaces());
        $this->assertSame($this->model, $this->resource->getModel());
        $this->assertNull($this->resource->getDriver());
        $this->assertNull($this->resource->getDriverManager());
        $this->assertNull($this->resource->getDriverMappingPath());
        $this->assertNull($this->resource->getDriverMappingFormat());
        $this->assertNull($this->resource->getRepository());
        $this->assertNull($this->resource->getFactory());
        $this->assertNull($this->resource->getForm());
        $this->assertNull($this->resource->getChoiceForm());
        $this->assertNull($this->resource->getDomainManager());
        $this->assertNull($this->resource->getController());
        $this->assertNull($this->resource->getIdPropertyPath());
        $this->assertNull($this->resource->getLabelPropertyPath());
        $this->assertEmpty($this->resource->getRelations());
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
        $this->resource->setDriverMappingPath($driverMappingPath = __DIR__);

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

    public function testDomainManager()
    {
        $this->resource->setDomainManager($domainManager = $this->createDomainManagerClassMock());

        $this->assertSame($domainManager, $this->resource->getDomainManager());
    }

    public function testController()
    {
        $this->resource->setController($controller = $this->createControllerClassMock());

        $this->assertSame($controller, $this->resource->getController());
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

    public function testSetRelations()
    {
        $this->resource->setRelations($relations = ['name' => $this->createResourceMock()]);

        $this->assertSame($relations, $this->resource->getRelations());
    }

    public function testAddRelation()
    {
        $this->resource->addRelation($name = 'name', $relation = $this->createResourceMock());

        $this->assertSame([$name => $relation], $this->resource->getRelations());
        $this->assertSame($relation, $this->resource->getRelation($name));
    }

    public function testRemoveRelation()
    {
        $this->resource->addRelation($name = 'name', $this->createResourceMock());
        $this->resource->removeRelation($name);

        $this->assertEmpty($this->resource->getRelations());
        $this->assertNull($this->resource->getRelation($name));
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
        return $this->createMock(DomainManagerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
