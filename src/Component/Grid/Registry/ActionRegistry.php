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

use Lug\Component\Grid\Action\Type\TypeInterface;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionRegistry extends Registry
{
    /**
     * @param TypeInterface[] $actions
     */
    public function __construct(array $actions = [])
    {
        parent::__construct(TypeInterface::class, $actions);
    }
}
