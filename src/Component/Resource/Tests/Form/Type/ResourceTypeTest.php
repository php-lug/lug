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
use Lug\Component\Resource\Form\Type\ResourceType;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var ResourceType
     */
    private $resourceType;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resourceType = new ResourceType();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->resourceType)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->resourceType);
    }

    public function testSubmit()
    {
        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model = \stdClass::class));

        $factory = $this->createFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($object = new \stdClass()));

        $form = $this->factory
            ->create(ResourceType::class, null, [
                'resource' => $resource,
                'factory'  => $factory,
            ])
            ->submit([]);

        $this->assertSame($resource, $form->getConfig()->getOption('resource'));
        $this->assertSame($factory, $form->getConfig()->getOption('factory'));
        $this->assertSame($model, $form->getConfig()->getOption('data_class'));
        $this->assertSame('lug.'.$name, $form->getConfig()->getOption('label_prefix'));
        $this->assertSame($object, $form->getData());

        $form->createView();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testCreateWithMissingOptions()
    {
        $this->factory->create(ResourceType::class);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testCreateWithInvalidResource()
    {
        $this->factory->create(ResourceType::class, null, [
            'resource' => 'foo',
            'factory'  => $this->createFactoryMock(),
        ]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testCreateWithInvalidRepository()
    {
        $this->factory->create(ResourceType::class, null, [
            'resource' => $this->createResourceMock(),
            'factory'  => 'foo',
        ]);
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
