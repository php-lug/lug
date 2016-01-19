<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Sort\Type;

use Lug\Bundle\GridBundle\Sort\Type\SortType;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Grid\Sort\Type\SortType as BaseSortType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SortTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SortType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new SortType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BaseSortType::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'trans_domain' => 'grids',
            'builder'      => $builder = $this->createDataSourceBuilderMock(),
            'sort'         => $sort = $this->createSortMock(),
            'grid'         => $grid = $this->createGridMock(),
        ], $resolver->resolve([
            'builder' => $builder,
            'sort'    => $sort,
            'grid'    => $grid,
        ]));
    }

    public function testConfigureOptionsWithTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'trans_domain' => 'domain',
            'builder'      => $this->createDataSourceBuilderMock(),
            'sort'         => $this->createSortMock(),
            'grid'         => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'trans_domain' => true,
            'builder'      => $this->createDataSourceBuilderMock(),
            'sort'         => $this->createSortMock(),
            'grid'         => $this->createGridMock(),
        ]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->getMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SortInterface
     */
    private function createSortMock()
    {
        return $this->getMock(SortInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }
}
