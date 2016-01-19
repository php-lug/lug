<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ResourceBundleInterface extends BundleInterface
{
    /**
     * @return ResourceInterface[]
     */
    public function getResources();

    /**
     * @return string
     */
    public function getAlias();
}
