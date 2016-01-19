<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Sort;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Grid\Sort\Sorter;
use Lug\Component\Grid\Sort\SorterInterface;
use Lug\Component\Grid\Sort\Type\TypeInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SorterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sorter
     */
    private $sorter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $sortRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->sortRegistry = $this->createServiceRegistryMock();
        $this->sorter = new Sorter($this->sortRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(SorterInterface::class, $this->sorter);
    }

    public function testSort()
    {
        $builder = $this->createDataSourceBuilderMock();
        $grid = $this->createGridMock();
        $data = [$sortName = 'foo' => $sortSort = 'ASC', $undefinedSort = 'bar' => 'DESC'];

        $grid
            ->expects($this->exactly(2))
            ->method('hasSort')
            ->will($this->returnValueMap([
                [$sortName, true],
                [$undefinedSort, false],
            ]));

        $grid
            ->expects($this->once())
            ->method('getSort')
            ->with($this->identicalTo($sortName))
            ->will($this->returnValue($sort = $this->createSortMock()));

        $sort
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type = 'type'));

        $sort
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options = ['baz' => 'bat']));

        $this->sortRegistry
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->will($this->returnValueMap([
                [$type, $sortType = $this->createSortTypeMock()],
                [$parentType = 'parent_type', $parentSortType = $this->createSortTypeMock()],
            ]));

        $sortType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $parentSortType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue(null));

        $sortType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->callback(function (OptionsResolver $resolver) use ($options) {
                $resolver->setDefined(array_merge(['builder', 'sort', 'grid'], array_keys($options)));

                return true;
            }));

        $parentSortType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class));

        $sortType
            ->expects($this->once())
            ->method('sort')
            ->with(
                $this->identicalTo($sortSort),
                $this->identicalTo(array_merge(
                    ['builder' => $builder, 'grid' => $grid, 'sort' => $sort],
                    $options
                ))
            )
            ->will($this->returnValue($result = 'result'));

        $this->sorter->sort($builder, $grid, $data);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->getMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SortInterface
     */
    private function createSortMock()
    {
        return $this->getMock(SortInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createSortTypeMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
