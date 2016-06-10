<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Column\Type;

use Lug\Component\Grid\Column\Type\ColumnType;
use Lug\Component\Grid\Column\Type\TypeInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new ColumnType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\BadMethodCallException
     * @expectedExceptionMessage The "name" column type is a virtual type, you can't use it directly.
     */
    public function testRender()
    {
        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->assertNull($this->type->render('data', ['column' => $column]));
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'column' => $this->createColumnMock(),
            'grid'   => $this->createGridViewMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingColumn()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['grid' => $this->createGridViewMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidColumn()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['grid' => $this->createGridViewMock(), 'column' => 'foo']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['column' => $this->createColumnMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['grid' => 'foo', 'column' => $this->createColumnMock()]);
    }

    public function testParent()
    {
        $this->assertNull($this->type->getParent());
    }

    public function testName()
    {
        $this->assertSame('column', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }
}
