<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\View;

use Lug\Bundle\GridBundle\View\GridView;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormView;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridView
     */
    private $view;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private $grid;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private $dataSourceBuilder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->grid = $this->createGridMock();
        $this->dataSourceBuilder = $this->createDataSourceBuilderMock();

        $this->view = new GridView($this->grid, $this->dataSourceBuilder);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridViewInterface::class, $this->view);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->grid, $this->view->getDefinition());
        $this->assertNull($this->view->getForm());
        $this->assertNull($this->view->getBatchForm());
    }

    public function testInitialState()
    {
        $form = $this->createFormViewMock();
        $batchForm = $this->createFormViewMock();

        $this->view = new GridView($this->grid, $this->dataSourceBuilder, $form, $batchForm);

        $this->assertSame($form, $this->view->getForm());
        $this->assertSame($batchForm, $this->view->getBatchForm());
    }

    public function testForm()
    {
        $this->view->setForm($form = $this->createFormViewMock());

        $this->assertSame($form, $this->view->getForm());
    }

    public function testBatchForm()
    {
        $this->view->setBatchForm($batchForm = $this->createFormViewMock());

        $this->assertSame($batchForm, $this->view->getBatchForm());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DataSourceBuilderInterface
     */
    private function createDataSourceBuilderMock()
    {
        return $this->createMock(DataSourceBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormView
     */
    private function createFormViewMock()
    {
        return $this->createMock(FormView::class);
    }
}
