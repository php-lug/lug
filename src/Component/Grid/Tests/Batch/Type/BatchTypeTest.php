<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Batcher\Type;

use Lug\Component\Grid\Batch\Type\BatchType;
use Lug\Component\Grid\Batch\Type\TypeInterface;
use Lug\Component\Grid\Model\BatchInterface;
use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BatchType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new BatchType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(TypeInterface::class, $this->type);
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\BadMethodCallException
     * @expectedExceptionMessage The "name" batch type is a virtual type, you can't use it directly.
     */
    public function testBatch()
    {
        $batch = $this->createBatchMock();
        $batch
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        $this->type->batch('data', ['batch' => $batch, 'grid' => $this->createGridMock()]);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'batch' => $this->createBatchMock(),
            'grid'  => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingBatch()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['grid' => $this->createGridMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidBatch()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['batch' => 'foo', 'grid' => $this->createGridMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['batch' => $this->createBatchMock()]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidGrid()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['batch' => $this->createBatchMock(), 'grid' => 'foo']);
    }

    public function testParent()
    {
        $this->assertNull($this->type->getParent());
    }

    public function testName()
    {
        $this->assertSame('batch', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|BatchInterface
     */
    private function createBatchMock()
    {
        return $this->createMock(BatchInterface::class);
    }
}
