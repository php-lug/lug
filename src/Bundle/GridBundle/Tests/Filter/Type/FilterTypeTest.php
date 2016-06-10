<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Filter\Type;

use Lug\Bundle\GridBundle\Filter\Type\FilterType;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Filter\Type\FilterType as BaseFilterType;
use Lug\Component\Grid\Model\FilterInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new FilterType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BaseFilterType::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'trans_domain' => 'grids',
            'builder'      => $builder = $this->createDataSourceBuilderMock(),
            'filter'       => $filter = $this->createFilterMock(),
            'grid'         => $grid = $this->createGridMock(),
        ], $resolver->resolve([
            'builder' => $builder,
            'filter'  => $filter,
            'grid'    => $grid,
        ]));
    }

    public function testConfigureOptionsWithTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'trans_domain' => 'domain',
            'builder'      => $this->createDataSourceBuilderMock(),
            'filter'       => $this->createFilterMock(),
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
            'filter'       => $this->createFilterMock(),
            'grid'         => $this->createGridMock(),
        ]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FilterInterface
     */
    private function createFilterMock()
    {
        return $this->createMock(FilterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }
}
