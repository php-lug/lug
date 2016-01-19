<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Batch\Type;

use Lug\Bundle\GridBundle\Batch\Type\BatchType;
use Lug\Component\Grid\Batch\Type\BatchType as BaseBatchType;
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
        $this->assertInstanceOf(BaseBatchType::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'trans_domain' => 'grids',
            'batch'        => $batch = $this->createBatchMock(),
            'grid'         => $grid = $this->createGridMock(),
        ], $resolver->resolve([
            'batch' => $batch,
            'grid'  => $grid,
        ]));
    }

    public function testConfigureOptionsWithTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'trans_domain' => 'domain',
            'batch'        => $this->createBatchMock(),
            'grid'         => $this->createGridMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'trans_domain' => true,
            'batch'        => $this->createBatchMock(),
            'grid'         => $this->createGridMock(),
        ]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|BatchInterface
     */
    private function createBatchMock()
    {
        return $this->getMock(BatchInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->getMock(GridInterface::class);
    }
}
