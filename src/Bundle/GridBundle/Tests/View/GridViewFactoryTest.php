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
use Lug\Bundle\GridBundle\View\GridViewFactory;
use Lug\Component\Grid\DataSource\DataSourceBuilderInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\View\GridViewFactoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridViewFactory
     */
    private $gridViewFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->gridViewFactory = new GridViewFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(GridViewFactoryInterface::class, $this->gridViewFactory);
    }

    public function testCreate()
    {
        $definition = $this->createGridMock();
        $dataSourceBuilder = $this->createDataSourceBuilderMock();

        $view = $this->gridViewFactory->create($definition, $dataSourceBuilder);

        $this->assertInstanceOf(GridView::class, $view);
        $this->assertSame($definition, $view->getDefinition());
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
}
