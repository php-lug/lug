<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle;

use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterActionPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterBatchFormSubscriberPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterBatchPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterColumnPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFilterFormPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFilterManagerStoragePass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFilterPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\RegisterSortPass;
use Lug\Bundle\GridBundle\DependencyInjection\Compiler\ReplaceLocaleContextPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugGridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RegisterActionPass())
            ->addCompilerPass(new RegisterBatchPass())
            ->addCompilerPass(new RegisterColumnPass())
            ->addCompilerPass(new RegisterFilterManagerStoragePass())
            ->addCompilerPass(new RegisterFilterPass())
            ->addCompilerPass(new RegisterSortPass())
            ->addCompilerPass(new RegisterBatchFormSubscriberPass())
            ->addCompilerPass(new RegisterFilterFormPass())
            ->addCompilerPass(new ReplaceLocaleContextPass());
    }
}
