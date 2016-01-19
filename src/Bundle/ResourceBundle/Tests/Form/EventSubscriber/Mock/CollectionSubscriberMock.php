<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form\EventSubscriber\Mock;

use Lug\Bundle\ResourceBundle\Form\EventSubscriber\CollectionSubscriber;
use Symfony\Component\Form\FormEvent;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CollectionSubscriberMock extends CollectionSubscriber
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function init(FormEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function manage(FormEvent $event)
    {
    }
}
