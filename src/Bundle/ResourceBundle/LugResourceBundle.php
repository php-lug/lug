<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle;

use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ConfigureResolveTargetEntitySubscriberPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterDomainManagerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterDriverMappingPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterFactoryPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterFlashListenerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterManagerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterManagerTagPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterMessageListenerPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterRepositoryPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcePass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ReplaceBase64FileExtensionPass;
use Lug\Bundle\ResourceBundle\DependencyInjection\Compiler\ReplaceBooleanExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugResourceBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RegisterResourcePass())
            ->addCompilerPass(new RegisterDriverMappingPass())
            ->addCompilerPass(new RegisterFactoryPass())
            ->addCompilerPass(new RegisterManagerTagPass())
            ->addCompilerPass(new RegisterManagerPass())
            ->addCompilerPass(new RegisterRepositoryPass())
            ->addCompilerPass(new RegisterDomainManagerPass())
            ->addCompilerPass(new ConfigureResolveTargetEntitySubscriberPass())
            ->addCompilerPass(new RegisterFlashListenerPass())
            ->addCompilerPass(new RegisterMessageListenerPass())
            ->addCompilerPass(new ReplaceBase64FileExtensionPass())
            ->addCompilerPass(new ReplaceBooleanExtensionPass());
    }
}
