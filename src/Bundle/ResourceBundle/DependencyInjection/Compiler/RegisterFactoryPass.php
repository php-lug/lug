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

use Lug\Bundle\RegistryBundle\DependencyInjection\Compiler\RegisterRegistryPass;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterFactoryPass extends RegisterRegistryPass
{
    public function __construct()
    {
        parent::__construct('lug.resource.registry.factory', 'lug.factory', 'resource');
    }
}
