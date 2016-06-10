<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Filter\Type;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Filter\Type\FilterType;
use Lug\Component\Grid\Filter\Type\TypeInterface;
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
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\BadMethodCallException
     * @expectedExceptionMessage The "name" filter type is a virtual type, you can't use it directly.
     */
    public function testFilter()
    {
        $filter = $this->createFilterMock();
        $filter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->type->filter('data', ['filter' => $filter]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'filter'  => $this->createFilterMock(),
            'grid'    => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingBuilder()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'filter' => $this->createFilterMock(),
            'grid'   => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidBuilder()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => 'foo',
            'filter'  => $this->createFilterMock(),
            'grid'    => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingFilter()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'grid'    => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidFilter()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'filter'  => 'foo',
            'grid'    => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'filter'  => $this->createFilterMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'filter'  => $this->createFilterMock(),
            'grid'    => 'foo',
        ], $resolver->resolve($options));
    }

    public function testParent()
    {
        $this->assertNull($this->type->getParent());
    }

    public function testName()
    {
        $this->assertSame('filter', $this->type->getName());
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
