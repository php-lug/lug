<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\Collection;
use Lug\Bundle\ResourceBundle\Util\ClassUtils;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * WARNING - This event subscriber is statefull.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CollectionSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $managerRegistry;

    /**
     * @var Collection|null
     */
    private $collection;

    /**
     * @var ResourceInterface[]
     */
    private $cache = [];

    /**
     * @param ServiceRegistryInterface $resourceRegistry
     * @param ServiceRegistryInterface $managerRegistry
     */
    public function __construct(ServiceRegistryInterface $resourceRegistry, ServiceRegistryInterface $managerRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param FormEvent $event
     */
    public function init(FormEvent $event)
    {
        $collection = $event->getData();

        if ($collection instanceof Collection) {
            $this->collection = clone $collection;
        }
    }

    /**
     * @param FormEvent $event
     */
    public function manage(FormEvent $event)
    {
        if ($this->collection === null || !$event->getForm()->isValid()) {
            return;
        }

        $collection = $event->getData();

        if (!$collection instanceof Collection) {
            return;
        }

        foreach ($this->collection as $element) {
            if ($collection->contains($element)) {
                continue;
            }

            if (($resource = $this->resolveResource(ClassUtils::getClass($element))) !== null) {
                $this->managerRegistry[$resource->getName()]->remove($element);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'init',
            FormEvents::POST_SUBMIT   => 'manage',
        ];
    }

    /**
     * @param string $class
     *
     * @return ResourceInterface|null
     */
    private function resolveResource($class)
    {
        if (array_key_exists($class, $this->cache)) {
            return $this->cache[$class];
        }

        $found = null;

        foreach ($this->resourceRegistry as $resource) {
            if ($resource->getModel() === $class) {
                $found = $resource;

                break;
            }
        }

        return $this->cache[$class] = $found;
    }
}
