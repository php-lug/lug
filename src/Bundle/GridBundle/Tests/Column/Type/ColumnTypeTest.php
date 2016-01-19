<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Column\Type;

use Lug\Bundle\GridBundle\Column\Type\ColumnType;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Component\Grid\Column\Type\ColumnType as BaseColumnType;
use Lug\Component\Grid\Model\ColumnInterface;
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
        $this->assertInstanceOf(BaseColumnType::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'trans_domain' => 'grids',
            'column'       => $column = $this->createColumnMock(),
            'grid'         => $grid = $this->createGridViewMock(),
        ], $resolver->resolve([
            'column' => $column,
            'grid'   => $grid,
        ]));
    }

    public function testConfigureOptionsWithTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'trans_domain' => 'domain',
            'column'       => $this->createColumnMock(),
            'grid'         => $this->createGridViewMock(),
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
            'column'       => $this->createColumnMock(),
            'grid'         => $this->createGridViewMock(),
        ]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->getMock(GridViewInterface::class);
    }
}
