<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Tools\ResolveTargetDocumentListener;
use Lug\Bundle\ResourceBundle\EventSubscriber\ResolveTargetSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResolveTargetDocumentSubscriber
    extends ResolveTargetDocumentListener
    implements EventSubscriber, ResolveTargetSubscriberInterface
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
        $this->addResolveTargetDocument($original, $new, []);
    }

    /**
     * {@inheritdoc}
     */
    public function addResolveTargetDocument($originalDocument, $newDocument, array $mapping)
    {
        $mapping['targetDocument'] = ltrim($newDocument, '\\\\');
        $this->resolveTargetEntities[ltrim($originalDocument, '\\\\')] = $mapping;

        parent::addResolveTargetDocument($originalDocument, $newDocument, $mapping);
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
                $map[$discriminatorName] = $this->resolveTargetEntities[$discriminatorClass]['targetDocument'];
            }
        }

        $metadata->setDiscriminatorMap($map);

        parent::loadClassMetadata($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }
}
