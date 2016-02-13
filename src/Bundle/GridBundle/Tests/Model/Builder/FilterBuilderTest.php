<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Model\Builder;

use Lug\Bundle\GridBundle\Model\Builder\FilterBuilder;
use Lug\Component\Grid\Model\Builder\FilterBuilder as BaseFilterBuilder;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $filterFormRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filterFormRegistry = $this->createServiceRegistryMock();
        $this->builder = new FilterBuilder($this->filterFormRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BaseFilterBuilder::class, $this->builder);
    }

    public function testBuild()
    {
        $this->filterFormRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($type = 'my.type'))
            ->will($this->returnValue($formType = $this->createFormTypeMock()));

        $filter = $this->builder->build([
            'name' => $name = 'my.name',
            'type' => $type,
        ]);

        $this->assertSame($name, $filter->getName());
        $this->assertSame($name, $filter->getLabel());
        $this->assertSame($type, $filter->getType());
        $this->assertSame(get_class($formType), $filter->getForm());
        $this->assertFalse($filter->hasFormOptions());
        $this->assertFalse($filter->hasOptions());
    }

    public function testBuildWithFullOptions()
    {
        $filter = $this->builder->build([
            'name'         => $name = 'my.name',
            'label'        => $label = 'my.label',
            'type'         => $type = 'my.type',
            'form'         => $form = get_class($this->createFormTypeMock()),
            'form_options' => $formOptions = ['foo' => 'bar'],
            'options'      => $options = ['baz' => 'bat'],
        ]);

        $this->assertSame($name, $filter->getName());
        $this->assertSame($label, $filter->getLabel());
        $this->assertSame($type, $filter->getType());
        $this->assertSame($form, $filter->getForm());
        $this->assertSame($formOptions, $filter->getFormOptions());
        $this->assertSame($options, $filter->getOptions());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormTypeInterface
     */
    private function createFormTypeMock()
    {
        return $this->getMock(FormTypeInterface::class);
    }
}
