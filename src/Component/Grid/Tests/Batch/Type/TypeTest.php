<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Batch\Type;

use Lug\Component\Grid\Batch\Type\AbstractType;
use Lug\Component\Grid\Batch\Type\TypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver = $this->createOptionsResolverMock();
        $resolver
            ->expects($this->never())
            ->method('setDefined');

        $resolver
            ->expects($this->never())
            ->method('setDefault');

        $resolver
            ->expects($this->never())
            ->method('setDefaults');

        $this->type->configureOptions($resolver);
    }

    public function testParent()
    {
        $this->assertSame('batch', $this->type->getParent());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|OptionsResolver
     */
    private function createOptionsResolverMock()
    {
        return $this->getMock(OptionsResolver::class);
    }
}
