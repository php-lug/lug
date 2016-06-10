<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests;

use Lug\Bundle\LocaleBundle\DependencyInjection\Compiler\RegisterValidationMetadataPass;
use Lug\Bundle\LocaleBundle\LugLocaleBundle;
use Lug\Bundle\ResourceBundle\AbstractResourceBundle;
use Lug\Component\Locale\Resource\LocaleResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugLocaleBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugLocaleBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new LugLocaleBundle();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractResourceBundle::class, $this->bundle);
    }

    public function testBuild()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterValidationMetadataPass::class));

        $this->bundle->build($container);
    }

    public function testResources()
    {
        $resources = $this->bundle->getResources();

        $this->assertCount(1, $resources);
        $this->assertArrayHasKey(0, $resources);
        $this->assertInstanceOf(LocaleResource::class, $resources[0]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->createMock(ContainerBuilder::class);
    }
}
