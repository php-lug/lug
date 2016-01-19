<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Batch;

use Lug\Bundle\GridBundle\Batch\Batcher;
use Lug\Bundle\GridBundle\Batch\BatcherInterface;
use Lug\Component\Grid\Batch\BatcherInterface as BaseBatcherInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Form\FormInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|ComponentBatcherInterface
     */
    private $lugBatcher;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->lugBatcher = $this->createBatcherMock();
        $this->batcher = new Batcher($this->lugBatcher);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BatcherInterface::class, $this->batcher);
    }

    public function testBatch()
    {
        $form = $this->createFormMock();
        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $form
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['type', $formType = $this->createFormMock()],
                ['value', $formValue = $this->createFormMock()],
            ]));

        $formType
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($type = 'type'));

        $formValue
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($value = new \ArrayIterator(['value'])));

        $this->lugBatcher
            ->expects($this->once())
            ->method('batch')
            ->with(
                $this->identicalTo($grid = $this->createGridMock()),
                $this->identicalTo($type),
                $this->identicalTo($value)
            );

        $this->batcher->batch($grid, $form);
    }

    public function testBatchWithInvalidForm()
    {
        $form = $this->createFormMock();
        $form
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->lugBatcher
            ->expects($this->never())
            ->method('batch');

        $this->batcher->batch($this->createGridMock(), $form);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ComponentBatcherInterface
     */
    private function createBatcherMock()
    {
        return $this->getMock(BaseBatcherInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }
}
