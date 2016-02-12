<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Registry;

use Lug\Component\Grid\Batch\Type\TypeInterface;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BatchRegistry extends Registry
{
    /**
     * @param TypeInterface[] $batches
     */
    public function __construct(array $batches = [])
    {
        parent::__construct(TypeInterface::class, $batches);
    }
}
