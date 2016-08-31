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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterMessageListenerPass extends AbstractRegisterGenericDomainListenerPass
{
    public function __construct()
    {
        parent::__construct(
            'lug.resource.listener.message',
            [
                'method'   => 'addMessage',
                'priority' => -1000,
            ],
            ['create', 'update', 'delete'],
            ['error', 'post']
        );
    }
}
