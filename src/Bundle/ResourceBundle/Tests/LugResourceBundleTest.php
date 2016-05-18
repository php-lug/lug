<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests;

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ConfigureResolveTargetEntitySubscriberPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterDomainManagerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterDriverMappingPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterFactoryPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterFlashListenerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterManagerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterManagerTagPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterMessageListenerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterRepositoryPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcePass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ReplaceBase64FileExtensionPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ReplaceBooleanExtensionPass;
use Lug\Bundle\ResourceBundle\LugResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugResourceBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugResourceBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new LugResourceBundle();
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
            ->with($this->isInstanceOf(RegisterResourcePass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterDriverMappingPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(2))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterFactoryPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(3))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterManagerTagPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(4))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterManagerPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(5))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterRepositoryPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(6))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterDomainManagerPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(7))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ConfigureResolveTargetEntitySubscriberPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(8))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterFlashListenerPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(9))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterMessageListenerPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(10))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ReplaceBase64FileExtensionPass::class))
            ->will($this->returnSelf());

        $container
            ->expects($this->at(11))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ReplaceBooleanExtensionPass::class))
            ->will($this->returnSelf());

        $this->bundle->build($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->getMock(ContainerBuilder::class);
    }
}
