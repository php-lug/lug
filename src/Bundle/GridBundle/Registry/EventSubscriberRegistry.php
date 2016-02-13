<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Registry;

use Lug\Component\Registry\Model\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class EventSubscriberRegistry extends Registry
{
    /**
     * @param EventSubscriberInterface[] $eventSubscribers
     */
    public function __construct(array $eventSubscribers = [])
    {
        parent::__construct(EventSubscriberInterface::class, $eventSubscribers);
    }
}
