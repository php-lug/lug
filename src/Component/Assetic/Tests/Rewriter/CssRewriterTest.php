<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Tests\Rewriter;

use Assetic\Asset\AssetInterface;
use Lug\Component\Assetic\Namer\NamerInterface;
use Lug\Component\Assetic\Rewriter\CssRewriter;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CssRewriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CssRewriter
     */
    private $cssRewriter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Packages
     */
    private $packages;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    private $filesystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|NamerInterface
     */
    private $namer;

    /**
     * @var string
     */
    private $webDirectory;

    /**
     * @var string
     */
    private $rewriteDirectory;

    /**
     * @var string
     */
    private $strategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->packages = $this->createPackagesMock();
        $this->filesystem = $this->createFilesystemMock();
        $this->namer = $this->createNamerMock();
        $this->webDirectory = sys_get_temp_dir();
        $this->rewriteDirectory = 'assets';
        $this->strategy = 'symlink';

        $this->cssRewriter = new CssRewriter(
            $this->packages,
            $this->filesystem,
            $this->namer,
            $this->webDirectory,
            $this->rewriteDirectory,
            $this->strategy
        );
    }

    /**
     * @expectedException \Lug\Component\Assetic\Exception\PathNotFoundException
     * @expectedExceptionMessage The web directory "/invalid" could not be found.
     */
    public function testInvalidWebDirectory()
    {
        new CssRewriter(
            $this->packages,
            $this->filesystem,
            $this->namer,
            '/invalid',
            $this->rewriteDirectory,
            $this->strategy
        );
    }

    /**
     * @expectedException \Lug\Component\Assetic\Exception\InvalidStrategyException
     * @expectedExceptionMessage The strategy "invalid" is not valid.
     */
    public function testInvalidStrategy()
    {
        new CssRewriter(
            $this->packages,
            $this->filesystem,
            $this->namer,
            $this->webDirectory,
            $this->rewriteDirectory,
            'invalid'
        );
    }

    public function testRewrite()
    {
        $asset = $this->createAssetMock();
        $asset
            ->expects($this->once())
            ->method('getSourceRoot')
            ->will($this->returnValue($sourceRoot = 'source_root'));

        $asset
            ->expects($this->exactly(2))
            ->method('getSourcePath')
            ->will($this->returnValue($sourcePath = 'source_path'));

        $asset
            ->expects($this->once())
            ->method('getTargetPath')
            ->will($this->returnValue($targetPath = 'target_path'));

        $reference = $this->createReference($path = 'foo.jpg');

        $this->filesystem
            ->expects($this->once())
            ->method('exists')
            ->with($this->identicalTo($absoluteSourcePath = $sourceRoot.DIRECTORY_SEPARATOR.$path))
            ->will($this->returnValue(true));

        $this->namer
            ->expects($this->once())
            ->method('name')
            ->with($this->identicalTo($absoluteSourcePath))
            ->will($this->returnValue($namedFile = 'named_file'));

        $webTargetPath = DIRECTORY_SEPARATOR.$this->rewriteDirectory.DIRECTORY_SEPARATOR.$namedFile;

        $this->filesystem
            ->expects($this->once())
            ->method($this->strategy)
            ->with(
                $this->identicalTo($absoluteSourcePath),
                $this->identicalTo($this->webDirectory.$webTargetPath),
                $this->isTrue()
            );

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->identicalTo($webTargetPath))
            ->will($this->returnValue($packagedPath = 'packaged_path'));

        $this->assertSame('url("'.$packagedPath.'")', $this->cssRewriter->rewrite($reference, $asset));
    }

    /**
     * @expectedException \Lug\Component\Assetic\Exception\PathNotFoundException
     * @expectedExceptionMessage The asset "source_root/foo.jpg" could not be found.
     */
    public function testRewriteWithNonexistentSourcePath()
    {
        $asset = $this->createAssetMock();
        $asset
            ->expects($this->once())
            ->method('getSourceRoot')
            ->will($this->returnValue($sourceRoot = 'source_root'));

        $asset
            ->expects($this->exactly(2))
            ->method('getSourcePath')
            ->will($this->returnValue($sourcePath = 'source_path'));

        $asset
            ->expects($this->once())
            ->method('getTargetPath')
            ->will($this->returnValue($targetPath = 'target_path'));

        $reference = $this->createReference($path = 'foo.jpg');

        $this->filesystem
            ->expects($this->once())
            ->method('exists')
            ->with($this->identicalTo($sourceRoot.DIRECTORY_SEPARATOR.$path))
            ->will($this->returnValue(false));

        $this->cssRewriter->rewrite($reference, $asset);
    }

    /**
     * @dataProvider pathNotRewritableProvider
     */
    public function testNotRewritable($path, $sourcePath = 'source_path', $targetPath = 'target_path')
    {
        $asset = $this->createAssetMock();
        $asset
            ->expects($this->any())
            ->method('getSourcePath')
            ->will($this->returnValue($sourcePath));

        $asset
            ->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue($targetPath));

        $reference = $this->createReference($path);

        $this->assertSame($reference[0], $this->cssRewriter->rewrite($reference, $asset));
    }

    /**
     * @return string[][]
     */
    public function pathNotRewritableProvider()
    {
        return [
            ['http://domain.com/asset.jpg'],
            ['https://domain.com/asset.jpg'],
            ['//domain.com/asset.jpg'],
            ['data:asset'],
            ['/asset.jpg'],
            ['asset.jpg', null],
            ['asset.jpg', 'source_path', null],
            ['asset.jpg', null, null],
            ['asset.jpg', 'path', 'path'],
        ];
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AssetInterface
     */
    private function createAssetMock()
    {
        return $this->getMock(AssetInterface::class);
    }

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function createReference($path = 'foo.jpg')
    {
        return [
            $url = 'url("'.($path).'")',
            '"',
            'url' => $path,
            $path,
            '"',
        ];
    }
}
