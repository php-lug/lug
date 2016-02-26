<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\EventSubscriber;

use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Resource\Domain\DomainEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleDomainSubscriber implements EventSubscriberInterface
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param LocaleProviderInterface   $localeProvider
     * @param TranslatorInterface       $translator
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(
        LocaleProviderInterface $localeProvider,
        TranslatorInterface $translator,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->localeProvider = $localeProvider;
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param DomainEvent $event
     */
    public function validateDefaultLocale(DomainEvent $event)
    {
        $object = $event->getObject();

        if ($object !== $this->localeProvider->getDefaultLocale()) {
            return;
        }

        $resource = $event->getResource();

        $event->setStopped(true);
        $event->setStatusCode(409);
        $event->setMessageType('error');

        $event->setMessage($this->translator->trans(
            'lug.'.$resource->getName().'.'.$event->getAction().'.default',
            ['%'.$resource->getName().'%' => $this->propertyAccessor->getValue(
                $object,
                $resource->getLabelPropertyPath()
            )],
            'flashes'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return ['lug.locale.pre_delete' => 'validateDefaultLocale'];
    }
}
