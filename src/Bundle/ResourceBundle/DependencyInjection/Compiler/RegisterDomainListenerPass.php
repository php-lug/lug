<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterDomainListenerPass extends RegisterListenersPass
{
    public function __construct()
    {
        parent::__construct(
            'lug.resource.domain.event_dispatcher',
            'lug.resource.domain.event_listener',
            'lug.resource.domain.event_subscriber'
        );
    }
}
