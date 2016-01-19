<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Registry;

use Lug\Component\Registry\Model\ServiceRegistry;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceRegistry extends ServiceRegistry
{
    /**
     * @param ResourceInterface[] $resources
     */
    public function __construct(array $resources = [])
    {
        parent::__construct(ResourceInterface::class, $resources);
    }
}
