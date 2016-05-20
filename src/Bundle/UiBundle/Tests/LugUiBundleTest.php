<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\Tests;

use Lug\Bundle\UiBundle\DependencyInjection\Compiler\RegisterMenuListenerPass;
use Lug\Bundle\UiBundle\DependencyInjection\Compiler\RegisterMenuPass;
use Lug\Bundle\UiBundle\LugUiBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugUiBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugUiBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new LugUiBundle();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testBuild()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterMenuListenerPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterMenuPass::class))
            ->will($this->returnSelf());

        $this->bundle->build($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->getMock(ContainerBuilder::class, ['addCompilerPass']);
    }
}
