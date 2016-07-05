<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Lug\Bundle\ResourceBundle\Util\ClassUtils;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Model\TranslatableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableResourceSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        $localeContext = $this->getLocaleContext();

        $entity->setLocales($localeContext->getLocales());
        $entity->setFallbackLocale($localeContext->getFallbackLocale());

        $class = ClassUtils::getClass($entity);

        foreach ($this->getResourceRegistry() as $resource) {
            if ($resource->getModel() === $class) {
                $entity->setTranslationClass($resource->getRelation('translation')->getModel());

                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postLoad];
    }

    /**
     * @return LocaleContextInterface
     */
    private function getLocaleContext()
    {
        return $this->container->get('lug.translation.context.locale');
    }

    /**
     * @return RegistryInterface
     */
    private function getResourceRegistry()
    {
        return $this->container->get('lug.resource.registry');
    }
}
