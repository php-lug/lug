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

use Lug\Component\Grid\Model\SortInterface;
use Lug\Component\Grid\Sort\Type\AbstractType;
use Lug\Component\Grid\Sort\Type\TypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbstractType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = $this->getMockForAbstractClass(AbstractType::class);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('sort');

        $sort = $this->createSortMock();
        $sort
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->type->configureOptions($resolver);

        $this->assertSame(['sort' => $sort, 'field' => $name], $resolver->resolve(['sort' => $sort]));
    }

    public function testConfigureOptionsWithField()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame(['field' => $field = 'field'], $resolver->resolve(['field' => $field]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidField()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['field' => true]);
    }

    public function testParent()
    {
        $this->assertSame('sort', $this->type->getParent());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SortInterface
     */
    private function createSortMock()
    {
        return $this->createMock(SortInterface::class);
    }
}
