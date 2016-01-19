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
use Lug\Component\Grid\Column\Type\TypeInterface;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractType
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

        $this->type = $this->getMockBuilder(AbstractType::class)
            ->setConstructorArgs([$this->propertyAccessor])
            ->getMockForAbstractClass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('column');

        $this->type->configureOptions($resolver);

        $column = $this->createColumnMock();
        $column
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->assertSame(['column' => $column, 'path' => $name], $resolver->resolve(['column' => $column]));
    }

    public function testConfigureOptionsWithExplicitPath()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = ['path' => 'path_value'], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidPath()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = ['path' => true], $resolver->resolve($options));
    }

    public function testParent()
    {
        $this->assertSame('column', $this->type->getParent());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->getMock(ColumnInterface::class);
    }
}
