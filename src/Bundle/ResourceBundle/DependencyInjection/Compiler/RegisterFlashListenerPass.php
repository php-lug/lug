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
class RegisterFlashListenerPass extends AbstractRegisterDomainListenerPass
{
    public function __construct()
    {
        parent::__construct('lug.resource.listener.flash', [
            'method'   => 'addFlash',
            'priority' => -2000,
        ]);
    }
}
