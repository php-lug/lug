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

use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB\ResolveTargetDocumentSubscriber;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM\ResolveTargetEntitySubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigureResolveTargetEntitySubscriberPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processDoctrineOrm($container);
        $this->processDoctrineMongoDb($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function processDoctrineOrm(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($definition = 'doctrine.orm.listeners.resolve_target_entity')) {
            return;
        }

        $resolveTargetEntity = $container
            ->getDefinition($definition)
            ->setClass(ResolveTargetEntitySubscriber::class)
            ->setConfigurator([new Reference('lug.resource.configurator.resolve_target_entity'), 'configure']);

        if (!$resolveTargetEntity->hasTag($tag = 'doctrine.event_subscriber')) {
            $resolveTargetEntity->addTag($tag);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function processDoctrineMongoDb(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($definition = 'doctrine_mongodb.odm.listeners.resolve_target_document')) {
            return;
        }

        $resolveTargetEntity = $container
            ->getDefinition($definition)
            ->setClass(ResolveTargetDocumentSubscriber::class)
            ->setConfigurator([new Reference('lug.resource.configurator.resolve_target_entity'), 'configure']);

        if (!$resolveTargetEntity->hasTag($tag = 'doctrine_mongodb.odm.event_subscriber')) {
            $resolveTargetEntity->addTag($tag);
        }
    }
}
