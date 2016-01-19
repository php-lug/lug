<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Form\Type;

use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Form\Type\AbstractResourceType;
use Lug\Component\Resource\Form\Type\ResourceType;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class AbstractResourceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var AbstractResourceType
     */
    private $resourceType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FactoryInterface
     */
    private $resourceFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->resourceFactory = $this->createFactoryMock();

        $this->resource
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('model'));

        $this->resourceType = $this->getMockBuilder(AbstractResourceType::class)
            ->setConstructorArgs([$this->resource, $this->resourceFactory])
            ->getMockForAbstractClass();

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addType(new ResourceType())
            ->addType($this->resourceType)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->resourceType);
        $this->assertSame(ResourceType::class, $this->resourceType->getParent());
    }

    public function testSubmit()
    {
        $form = $this->formFactory
            ->create(get_class($this->resourceType))
            ->submit([]);

        $this->assertSame($this->resource, $form->getConfig()->getOption('resource'));
        $this->assertSame($this->resourceFactory, $form->getConfig()->getOption('factory'));

        $form->createView();
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
