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

use Lug\Component\Grid\Column\Type\AbstractType;
use Lug\Component\Grid\Column\Type\TextType;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TextTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TextType
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->type = new TextType($this->propertyAccessor);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    public function testRender()
    {
        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->type->render($data, ['path' => $path]));
    }

    public function testRenderWithNull()
    {
        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue(null));

        $this->assertNull($this->type->render($data, ['path' => $path]));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\InvalidTypeException
     * @expectedExceptionMessage The "name" text column type expects a scalar value, got "stdClass".
     */
    public function testRenderWithoutScalar()
    {
        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($path = 'path_value')
            )
            ->will($this->returnValue(new \stdClass()));

        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->type->render($data, ['column' => $column, 'path' => $path]);
    }

    public function testName()
    {
        $this->assertSame('text', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->createMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }
}
