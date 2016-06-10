<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\DataSource;

use Lug\Component\Grid\DataSource\DataSourceInterface;
use Lug\Component\Grid\DataSource\PagerfantaDataSource;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PagerfantaDataSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PagerfantaDataSource
     */
    private $dataSource;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->dataSource = new PagerfantaDataSource($this->createAdapterMock());
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(DataSourceInterface::class, $this->dataSource);
        $this->assertInstanceOf(Pagerfanta::class, $this->dataSource);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AdapterInterface
     */
    private function createAdapterMock()
    {
        return $this->createMock(AdapterInterface::class);
    }
}
