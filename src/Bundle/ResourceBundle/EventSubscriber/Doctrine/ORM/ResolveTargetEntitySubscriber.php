<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Lug\Bundle\ResourceBundle\EventSubscriber\ResolveTargetSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetEntitySubscriber extends ResolveTargetEntityListener implements ResolveTargetSubscriberInterface
{
    /**
     * @var mixed[]
     */
    private $resolveTargetEntities = [];

    /**
     * {@inheritdoc}
     */
    public function addResolveTarget($original, $new)
    {
        $this->addResolveTargetEntity($original, $new, []);
    }

    /**
     * {@inheritdoc}
     */
    public function addResolveTargetEntity($originalEntity, $newEntity, array $mapping)
    {
        $mapping['targetEntity'] = ltrim($newEntity, '\\\\');
        $this->resolveTargetEntities[ltrim($originalEntity, '\\\\')] = $mapping;

        parent::addResolveTargetEntity($originalEntity, $newEntity, $mapping);
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $map = [];
        $metadata = $event->getClassMetadata();

        foreach ($metadata->discriminatorMap as $discriminatorName => $discriminatorClass) {
            if (isset($this->resolveTargetEntities[$discriminatorClass])) {
                $map[$discriminatorName] = $this->resolveTargetEntities[$discriminatorClass]['targetEntity'];
            }
        }

        $metadata->setDiscriminatorMap($map);

        parent::loadClassMetadata($event);
    }
}
