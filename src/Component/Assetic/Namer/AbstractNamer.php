<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Namer;

use Assetic\Asset\AssetInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractNamer implements NamerInterface
{
    /**
     * {@inheritdoc}
     */
    public function name($path, AssetInterface $asset)
    {
        return $this->doName($path, $asset).'.'.pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @param string         $path
     * @param AssetInterface $asset
     *
     * @return string
     */
    abstract protected function doName($path, AssetInterface $asset);
}
