<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Batcher;

use Lug\Component\Grid\Batch\Batcher;
use Lug\Component\Grid\Batch\BatcherInterface;
use Lug\Component\Grid\Batch\Type\TypeInterface;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Batcher
     */
    private $batcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $batchRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->batchRegistry = $this->createServiceRegistryMock();
        $this->batcher = new Batcher($this->batchRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BatcherInterface::class, $this->batcher);
    }

    public function testBatch()
    {
        $grid = $this->createGridMock();
        $batch = 'delete';
        $data = ['data'];

        $grid
            ->expects($this->once())
            ->method('getBatch')
            ->with($this->identicalTo($batch))
            ->will($this->returnValue($gridBatch = $this->createBatchMock()));

        $gridBatch
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type = 'type'));

        $gridBatch
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options = ['foo' => 'bar']));

        $this->batchRegistry
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->will($this->returnValueMap([
                [$type, $batchType = $this->createBatchTypeMock()],
                [$parentType = 'parent_type', $parentBatchType = $this->createBatchTypeMock()],
            ]));

        $batchType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $parentBatchType
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue(null));

        $batchType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->callback(function (OptionsResolver $resolver) use ($options) {
                $resolver->setDefined(array_merge(['batch', 'grid'], array_keys($options)));

                return true;
            }));

        $parentBatchType
            ->expects($this->once())
            ->method('configureOptions')
            ->with($this->isInstanceOf(OptionsResolver::class));

        $batchType
            ->expects($this->once())
            ->method('batch')
            ->with(
                $this->identicalTo($data),
                $this->identicalTo(array_merge(['batch' => $gridBatch, 'grid' => $grid], $options))
            );

        $this->batcher->batch($grid, $batch, $data);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|BatchInterface
     */
    private function createBatchMock()
    {
        return $this->getMock(BatchInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createBatchTypeMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
