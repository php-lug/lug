<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Filter;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Filter\Filterer;
use Lug\Component\Grid\Filter\FiltererInterface;
use Lug\Component\Grid\Filter\Type\TypeInterface;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FiltererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filterer
     */
    private $filterer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $filterRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filterRegistry = $this->createServiceRegistryMock();
        $this->filterer = new Filterer($this->filterRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FiltererInterface::class, $this->filterer);
    }

    public function testFilter()
    {
        $builder = $this->createDataSourceBuilderMock();
        $grid = $this->createGridMock();
        $data = [$filterName = 'foo' => $filterSort = 'ASC', $undefinedFilter = 'bar' => 'DESC'];

        $grid
            ->expects($this->exactly(2))
            ->method('hasFilter')
            ->will($this->returnValueMap([
                [$filterName, true],
                [$undefinedFilter, false],
            ]));

        $grid
            ->expects($this->once())
            ->method('getFilter')
            ->with($this->identicalTo($filterName))
            ->will($this->returnValue($filter = $this->createFilterMock()));

        $filter
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type = 'type'));

        $filter
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options = ['baz' => 'bat']));

        $this->filterRegistry
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->will($this->returnValueMap([
                [$type, $filterType = $this->createFilterTypeMock()],
                [$parentType = 'parent_type', $parentFilterType = $this->createFilterTypeMock()],
            ]));

        $filterType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $parentFilterType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue(null));

        $filterType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->callback(function (OptionsResolver $resolver) use ($options) {
                $resolver->setDefined(array_merge(['builder', 'filter', 'grid'], array_keys($options)));

                return true;
            }));

        $parentFilterType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class));

        $filterType
            ->expects($this->once())
            ->method('filter')
            ->with(
                $this->identicalTo($filterSort),
                $this->identicalTo(array_merge(
                    ['filter' => $filter, 'grid' => $grid, 'builder' => $builder],
                    $options
                ))
            )
            ->will($this->returnValue($result = 'result'));

        $this->filterer->filter($builder, $grid, $data);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->createMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterInterface
     */
    private function createFilterMock()
    {
        return $this->createMock(FilterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createFilterTypeMock()
    {
        return $this->createMock(TypeInterface::class);
    }
}
