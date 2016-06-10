<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Action\Type;

use Lug\Component\Grid\Action\Type\ActionType;
use Lug\Component\Grid\Action\Type\TypeInterface;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new ActionType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\BadMethodCallException
     * @expectedExceptionMessage The "name" action type is a virtual type, you can't use it directly.
     */
    public function testRender()
    {
        $action = $this->createActionMock();
        $action
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->type->render('data', ['action' => $action]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'action' => $this->createActionMock(),
            'grid'   => $this->createGridViewMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingAction()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['grid' => $this->createGridViewMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidAction()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['action' => 'foo', 'grid' => $this->createGridViewMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['action' => $this->createActionMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['action' => $this->createActionMock(), 'grid' => 'foo']);
    }

    public function testParent()
    {
        $this->assertNull($this->type->getParent());
    }

    public function testName()
    {
        $this->assertSame('action', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->createMock(ActionInterface::class);
    }
}
