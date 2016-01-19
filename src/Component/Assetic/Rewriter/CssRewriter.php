<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Rewriter;

use Assetic\Asset\AssetInterface;
use Lug\Component\Assetic\Exception\InvalidStrategyException;
use Lug\Component\Assetic\Exception\PathNotFoundException;
use Lug\Component\Assetic\Namer\NamerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CssRewriter
{
    const STRATEGY_COPY = 'copy';
    const STRATEGY_SYMLINK = 'symlink';

    /**
     * @var Packages
     */
    private $packages;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var NamerInterface
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
     * @param Packages       $packages
     * @param Filesystem     $filesystem
     * @param NamerInterface $namer
     * @param string         $webDirectory
     * @param string         $rewriteDirectory
     * @param string         $strategy
     */
    public function __construct(
        Packages $packages,
        Filesystem $filesystem,
        NamerInterface $namer,
        $webDirectory,
        $rewriteDirectory,
        $strategy
    ) {
        $this->packages = $packages;
        $this->filesystem = $filesystem;
        $this->namer = $namer;
        $this->webDirectory = realpath($webDirectory);

        if ($this->webDirectory === false) {
            throw new PathNotFoundException(sprintf('The web directory "%s" could not be found.', $webDirectory));
        }

        $this->rewriteDirectory = $rewriteDirectory[0] !== DIRECTORY_SEPARATOR
            ? DIRECTORY_SEPARATOR.$rewriteDirectory
            : $rewriteDirectory;

        if (!in_array($strategy, [self::STRATEGY_COPY, self::STRATEGY_SYMLINK], true)) {
            throw new InvalidStrategyException(sprintf('The strategy "%s" is not valid.', $strategy));
        }

        $this->strategy = $strategy;
    }

    /**
     * @param mixed[]        $reference
     * @param AssetInterface $asset
     *
     * @return string
     */
    public function rewrite(array $reference, AssetInterface $asset)
    {
        if (!$this->isRewritable($reference['url'], $asset->getSourcePath(), $asset->getTargetPath())) {
            return $reference[0];
        }

        $absoluteSourcePath = $this->getAssetPath(
            dirname($this->createAssetPath($asset->getSourceRoot(), $asset->getSourcePath())),
            parse_url($reference['url'])['path']
        );

        $webTargetPath = $this->createAssetPath(
            $this->rewriteDirectory,
            $this->namer->name($absoluteSourcePath, $asset)
        );

        $absoluteTargetPath = $this->webDirectory.$webTargetPath;
        $this->filesystem->{$this->strategy}($absoluteSourcePath, $absoluteTargetPath, true);

        return str_replace($reference['url'], $this->packages->getUrl($webTargetPath), $reference[0]);
    }

    /**
     * @param string      $path
     * @param string|null $sourcePath
     * @param string|null $targetPath
     *
     * @return bool
     */
    private function isRewritable($path, $sourcePath, $targetPath)
    {
        return strpos($path, '//') === false
            && strpos($path, 'data:') !== 0
            && $path[0] !== '/'
            && $sourcePath !== null
            && $targetPath !== null
            && $sourcePath !== $targetPath;
    }

    /**
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function getAssetPath($prefix, $suffix)
    {
        $path = $this->createAssetPath($prefix, $suffix);

        if (!$this->filesystem->exists($path)) {
            throw new PathNotFoundException(sprintf('The asset "%s" could not be found.', $path));
        }

        return $path;
    }

    /**
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function createAssetPath($prefix, $suffix)
    {
        return $prefix.DIRECTORY_SEPARATOR.$suffix;
    }
}
