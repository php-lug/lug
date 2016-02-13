<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Column;

use Lug\Component\Grid\Column\ColumnRenderer;
use Lug\Component\Grid\Column\ColumnRendererInterface;
use Lug\Component\Grid\Column\Type\TypeInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ColumnRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnRenderer
     */
    private $renderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $columnRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->columnRegistry = $this->createServiceRegistryMock();
        $this->renderer = new ColumnRenderer($this->columnRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ColumnRendererInterface::class, $this->renderer);
    }

    public function testRender()
    {
        $grid = $this->createGridViewMock();
        $column = $this->createColumnMock();
        $data = 'data';

        $column
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type = 'type'));

        $column
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options = ['baz' => 'bat']));

        $this->columnRegistry
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->will($this->returnValueMap([
                [$type, $columnType = $this->createColumnTypeMock()],
                [$parentType = 'parent_type', $parentColumnType = $this->createColumnTypeMock()],
            ]));

        $columnType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $parentColumnType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue(null));

        $columnType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->callback(function (OptionsResolver $resolver) use ($options) {
                $resolver->setDefined(array_merge(['column', 'grid'], array_keys($options)));

                return true;
            }));

        $parentColumnType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class));

        $columnType
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($data),
                $this->identicalTo(array_merge(['column' => $column, 'grid' => $grid], $options))
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->render($grid, $column, $data));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->getMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createColumnTypeMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
