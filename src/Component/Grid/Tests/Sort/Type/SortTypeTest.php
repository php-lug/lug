<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Sort\Type;

use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Grid\Sort\Type\SortType;
use Lug\Component\Grid\Sort\Type\TypeInterface;
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
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\BadMethodCallException
     * @expectedExceptionMessage The "name" sort type is a virtual type, you can't use it directly.
     */
    public function testSort()
    {
        $sort = $this->createSortMock();
        $sort
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->type->sort('data', ['sort' => $sort]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'sort'    => $this->createSortMock(),
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
            'sort' => $this->createSortMock(),
            'grid' => $this->createGridMock(),
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
            'sort'    => $this->createSortMock(),
            'grid'    => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingSort()
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
    public function testConfigureOptionsWithInvalidSort()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'builder' => $this->createDataSourceBuilderMock(),
            'sort'    => 'foo',
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
            'sort'    => $this->createSortMock(),
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
            'sort'    => $this->createSortMock(),
            'grid'    => 'foo',
        ], $resolver->resolve($options));
    }

    public function testParent()
    {
        $this->assertNull($this->type->getParent());
    }

    public function testName()
    {
        $this->assertSame('sort', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SortInterface
     */
    private function createSortMock()
    {
        return $this->createMock(SortInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }
}
