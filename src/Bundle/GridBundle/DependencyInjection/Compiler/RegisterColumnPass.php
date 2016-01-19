<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\DependencyInjection\Compiler;

use Lug\Bundle\RegistryBundle\DependencyInjection\Compiler\AbstractRegisterServiceRegistryPass;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterColumnPass extends AbstractRegisterServiceRegistryPass
{
    public function __construct()
    {
        parent::__construct('lug.grid.registry.column', 'lug.grid.column');
    }
}
