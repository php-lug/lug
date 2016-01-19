<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Handler;

use Lug\Bundle\GridBundle\Handler\GridHandler;
use Lug\Bundle\GridBundle\Handler\GridHandlerInterface;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Component\Grid\Handler\GridHandlerInterface as BaseGridHandlerInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridHandler
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ComponentGridHandlerInterface
     */
    private $lugHandler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->lugHandler = $this->createGridHandlerMock();
        $this->handler = new GridHandler($this->lugHandler);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridHandlerInterface::class, $this->handler);
    }

    public function testHandle()
    {
        $grid = $this->createGridMock();
        $form = $this->createFormMock();
        $batchForm = $this->createFormMock();

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $form
            ->expects($this->exactly(4))
            ->method('get')
            ->will($this->returnValueMap([
                ['filters', $filtersForm = $this->createFormMock()],
                ['sorting', $sortingForm = $this->createFormMock()],
                ['page', $pageForm = $this->createFormMock()],
                ['limit', $limitForm = $this->createFormMock()],
            ]));

        $filtersForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($filters = ['filter']));

        $sortingForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($sorting = ['sort']));

        $pageForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($page = 2));

        $limitForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($limit = 20));

        $this->lugHandler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($grid),
                $this->identicalTo($filters),
                $this->identicalTo($sorting),
                $this->identicalTo(['page' => $page, 'limit' => $limit])
            )
            ->will($this->returnValue($view = $this->createGridViewMock()));

        $form
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($formView = $this->createFormViewMock()));

        $batchForm
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($batchFormView = $this->createFormViewMock()));

        $view
            ->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($formView));

        $view
            ->expects($this->once())
            ->method('setBatchForm')
            ->with($this->identicalTo($batchFormView));

        $this->assertSame($view, $this->handler->handle($grid, $form, $batchForm));
    }

    public function testHandleWithInvalidForm()
    {
        $grid = $this->createGridMock();
        $form = $this->createFormMock();
        $batchForm = $this->createFormMock();

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->lugHandler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($grid),
                $this->identicalTo([]),
                $this->identicalTo([]),
                $this->identicalTo([])
            )
            ->will($this->returnValue($view = $this->createGridViewMock()));

        $form
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($formView = $this->createFormViewMock()));

        $batchForm
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($batchFormView = $this->createFormViewMock()));

        $view
            ->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($formView));

        $view
            ->expects($this->once())
            ->method('setBatchForm')
            ->with($this->identicalTo($batchFormView));

        $this->assertSame($view, $this->handler->handle($grid, $form, $batchForm));
    }

    public function testHandleWithoutForm()
    {
        $grid = $this->createGridMock();
        $batchForm = $this->createFormMock();

        $this->lugHandler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($grid),
                $this->identicalTo([]),
                $this->identicalTo([]),
                $this->identicalTo([])
            )
            ->will($this->returnValue($view = $this->createGridViewMock()));

        $batchForm
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($batchFormView = $this->createFormViewMock()));

        $view
            ->expects($this->once())
            ->method('setBatchForm')
            ->with($this->identicalTo($batchFormView));

        $this->assertSame($view, $this->handler->handle($grid, null, $batchForm));
    }

    public function testHandleWithoutBatchForm()
    {
        $grid = $this->createGridMock();
        $form = $this->createFormMock();

        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $form
            ->expects($this->exactly(4))
            ->method('get')
            ->will($this->returnValueMap([
                ['filters', $filtersForm = $this->createFormMock()],
                ['sorting', $sortingForm = $this->createFormMock()],
                ['page', $pageForm = $this->createFormMock()],
                ['limit', $limitForm = $this->createFormMock()],
            ]));

        $filtersForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($filters = ['filter']));

        $sortingForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($sorting = ['sort']));

        $pageForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($page = 2));

        $limitForm
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($limit = 20));

        $this->lugHandler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($grid),
                $this->identicalTo($filters),
                $this->identicalTo($sorting),
                $this->identicalTo(['page' => $page, 'limit' => $limit])
            )
            ->will($this->returnValue($view = $this->createGridViewMock()));

        $form
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($formView = $this->createFormViewMock()));

        $view
            ->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($formView));

        $this->assertSame($view, $this->handler->handle($grid, $form));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ComponentGridHandlerInterface
     */
    private function createGridHandlerMock()
    {
        return $this->getMock(BaseGridHandlerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->getMock(GridViewInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormView
     */
    private function createFormViewMock()
    {
        return $this->getMock(FormView::class);
    }
}
