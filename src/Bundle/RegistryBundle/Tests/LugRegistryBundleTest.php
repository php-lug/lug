<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\RegistryBundle\Tests;

use Lug\Bundle\RegistryBundle\DependencyInjection\Compiler\ConvertRegistryPass;
use Lug\Bundle\RegistryBundle\LugRegistryBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugRegistryBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugRegistryBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new LugRegistryBundle();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testCompilerPass()
    {
        $container = $this->createContainerMock();
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ConvertRegistryPass::class));

        $this->bundle->build($container);
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerMock()
    {
        return $this->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['addCompilerPass'])
            ->getMock();
    }
}
