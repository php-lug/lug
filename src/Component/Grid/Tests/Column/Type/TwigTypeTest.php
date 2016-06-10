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
use Lug\Component\Grid\Column\Type\TwigType;
use Lug\Component\Grid\Model\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TwigTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigType
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    private $twig;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->twig = $this->createTwigEnvironmentMock();

        $this->type = new TwigType($this->propertyAccessor, $this->twig);
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
            ->will($this->returnValue($twigData = 'twig_data'));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($template = 'template'),
                $this->identicalTo(array_merge(
                    $context = ['foo' => 'bar'],
                    ['column' => $column = $this->createColumnMock(), 'data' => $twigData]
                ))
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->type->render($data, [
            'column'   => $column,
            'path'     => $path,
            'template' => $template,
            'context'  => $context,
        ]));
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'path'     => $path = 'path_value',
            'context'  => [],
            'template' => $template = 'template_name',
        ], $resolver->resolve(array_merge(['path' => $path, 'template' => $template])));
    }

    public function testConfigureOptionsWithContext()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'path'     => 'path_value',
            'context'  => ['context'],
            'template' => 'template_name',
        ], $resolver->resolve($options));
    }

    public function testName()
    {
        $this->assertSame('twig', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->createMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    private function createTwigEnvironmentMock()
    {
        return $this->createMock(\Twig_Environment::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock()
    {
        return $this->createMock(ColumnInterface::class);
    }
}
