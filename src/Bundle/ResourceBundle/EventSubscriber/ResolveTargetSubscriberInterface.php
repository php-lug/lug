<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventSubscriber;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ResolveTargetSubscriberInterface
{
    /**
     * @param string $original
     * @param string $new
     */
    public function addResolveTarget($original, $new);
}
