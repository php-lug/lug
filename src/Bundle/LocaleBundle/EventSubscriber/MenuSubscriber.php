<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\EventSubscriber;

use Lug\Bundle\UiBundle\Menu\MenuBuilderEvent;
use Lug\Bundle\UiBundle\Menu\MenuBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function onBuild(MenuBuilderEvent $event)
    {
        $item = $event->getItem();

        if ($item->getName() === 'lug.admin.sidebar') {
            $item->addChild($event->getFactory()->createItem('locale', [
                'route'           => 'lug_admin_locale_index',
                'label'           => 'lug.admin.menu.sidebar.locale',
                'labelAttributes' => ['icon' => 'language'],
                'extras'          => ['routes' => [['pattern' => '/^lug_admin_locale_.+$/']]],
            ]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [MenuBuilderEvents::BUILD => 'onBuild'];
    }
}
