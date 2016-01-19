<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\EventSubscriber\Filter;

use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimeFilterSubscriber extends DateTimeFilterSubscriber
{
    public function __construct()
    {
        parent::__construct(TimeType::class);
    }
}
