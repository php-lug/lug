<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Action;

use Lug\Component\Grid\Action\ActionRenderer;
use Lug\Component\Grid\Action\ActionRendererInterface;
use Lug\Component\Grid\Action\Type\TypeInterface;
use Lug\Component\Grid\Model\ActionInterface;
use Lug\Component\Grid\View\GridViewInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionRenderer
     */
    private $renderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $actionRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->actionRegistry = $this->createServiceRegistryMock();
        $this->renderer = new ActionRenderer($this->actionRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ActionRendererInterface::class, $this->renderer);
    }

    public function testRender()
    {
        $grid = $this->createGridViewMock();
        $action = $this->createActionMock();
        $data = 'data';

        $action
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type = 'type'));

        $action
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options = ['baz' => 'bat']));

        $this->actionRegistry
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->will($this->returnValueMap([
                [$type, $actionType = $this->createActionTypeMock()],
                [$parentType = 'parent_type', $parentActionType = $this->createActionTypeMock()],
            ]));

        $actionType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $parentActionType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue(null));

        $actionType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->callback(function (OptionsResolver $resolver) use ($options) {
                $resolver->setDefined(array_merge(['action', 'grid'], array_keys($options)));

                return true;
            }));

        $parentActionType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class));

        $actionType
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo($data),
                $this->identicalTo(array_merge(['action' => $action, 'grid' => $grid], $options))
            )
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->renderer->render($grid, $action, $data));
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
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->getMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createActionTypeMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
