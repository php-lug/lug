<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AsseticBundle\Tests\DependencyInjection;

use Lug\Bundle\AsseticBundle\DependencyInjection\LugAsseticExtension;
use Lug\Component\Assetic\Filter\CssRewriteFilter;
use Lug\Component\Assetic\Namer\NamerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugAsseticExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugAsseticExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Packages
     */
    private $packages;

    /**
     * @var\PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    private $filesystem;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new LugAsseticExtension();
        $this->kernelRootDir = __DIR__.'/Fixtures/app';
        $this->packages = $this->createPackagesMock();
        $this->filesystem = $this->createFilesystemMock();

        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.root_dir', $this->kernelRootDir);
        $this->container->set('assets.packages', $this->packages);
        $this->container->set('filesystem', $this->filesystem);
        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testCssRewriteDisabled()
    {
        $this->compileContainer();

        $this->assertFalse($this->container->hasDefinition('lug.assetic.filter.css_rewrite'));
        $this->assertFalse($this->container->hasDefinition('lug.assetic.rewriter.css'));
        $this->assertFalse($this->container->hasDefinition('lug.assetic.namer.md5'));
    }

    public function testCssRewrite()
    {
        $this->compileContainer('css_rewrite');

        $this->assertTrue($this->container->hasDefinition('lug.assetic.filter.css_rewrite'));
        $this->assertTrue($this->container->hasDefinition('lug.assetic.rewriter.css'));
        $this->assertTrue($this->container->hasDefinition('lug.assetic.namer.md5'));

        $cssRewriteFilterDefinition = $this->container->getDefinition('lug.assetic.filter.css_rewrite');
        $cssRewriterDefinition = $this->container->getDefinition('lug.assetic.rewriter.css');

        $this->assertTrue($cssRewriteFilterDefinition->hasTag('assetic.filter'));
        $this->assertSame([['alias' => 'lug_css_rewrite']], $cssRewriteFilterDefinition->getTag('assetic.filter'));
        $this->assertSame('lug.assetic.rewriter.css', (string) $cssRewriteFilterDefinition->getArgument(0));

        $this->assertSame('assets.packages', (string) $cssRewriterDefinition->getArgument(0));
        $this->assertSame('filesystem', (string) $cssRewriterDefinition->getArgument(1));
        $this->assertSame('lug.assetic.namer.md5', (string) $cssRewriterDefinition->getArgument(2));
        $this->assertSame($this->kernelRootDir.'/../web', $cssRewriterDefinition->getArgument(3));
        $this->assertSame('/assets', $cssRewriterDefinition->getArgument(4));
        $this->assertSame('copy', $cssRewriterDefinition->getArgument(5));

        $this->assertInstanceOf(CssRewriteFilter::class, $this->container->get('lug.assetic.filter.css_rewrite'));
    }

    public function testCssRewriterNamer()
    {
        $this->container->set('lug.assetic.namer.custom', $this->createNamerMock());
        $this->compileContainer('css_rewrite_namer');

        $this->assertSame(
            'lug.assetic.namer.custom',
            (string) $this->container->getDefinition('lug.assetic.rewriter.css')->getArgument(2)
        );

        $this->assertInstanceOf(CssRewriteFilter::class, $this->container->get('lug.assetic.filter.css_rewrite'));
    }

    public function testCssRewriterWebDirectory()
    {
        $this->compileContainer('css_rewrite_web_directory');

        $this->assertSame(
            __DIR__.'/Fixtures/app/../web-bis',
            $this->container->getDefinition('lug.assetic.rewriter.css')->getArgument(3)
        );

        $this->assertInstanceOf(CssRewriteFilter::class, $this->container->get('lug.assetic.filter.css_rewrite'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /The css rewrite web directory "(.*)" does not exist\.$/
     */
    public function testCssRewriterWebDirectoryInvalid()
    {
        $this->compileContainer('css_rewrite_web_directory_invalid');
    }

    public function testCssRewriterRewriteDirectory()
    {
        $this->compileContainer('css_rewrite_rewrite_directory');

        $this->assertSame(
            '/compiled',
            $this->container->getDefinition('lug.assetic.rewriter.css')->getArgument(4)
        );

        $this->assertInstanceOf(CssRewriteFilter::class, $this->container->get('lug.assetic.filter.css_rewrite'));
    }

    public function testCssRewriterStrategy()
    {
        $this->compileContainer('css_rewrite_strategy');

        $this->assertSame(
            'symlink',
            $this->container->getDefinition('lug.assetic.rewriter.css')->getArgument(5)
        );

        $this->assertInstanceOf(CssRewriteFilter::class, $this->container->get('lug.assetic.filter.css_rewrite'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The css rewrite strategy "foo" is not supported.
     */
    public function testCssRewriterStrategyInvalid()
    {
        $this->compileContainer('css_rewrite_strategy_invalid');
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    /**
     * @param string|null $configuration
     */
    private function compileContainer($configuration = null)
    {
        if ($configuration !== null) {
            $this->loadConfiguration($this->container, $configuration);
        }

        $this->container->compile();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Packages
     */
    private function createPackagesMock()
    {
        return $this->getMock(Packages::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    private function createFilesystemMock()
    {
        return $this->getMock(Filesystem::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|NamerInterface
     */
    private function createNamerMock()
    {
        return $this->getMock(NamerInterface::class);
    }
}
