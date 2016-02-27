<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Resource;

use Lug\Bundle\ResourceBundle\Controller\Controller;
use Lug\Component\Locale\Resource\LocaleResource as LugLocaleResource;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleResource extends LugLocaleResource
{
    /**
     * @param string $bundlePath
     * @param string $driver
     */
    public function __construct($bundlePath, $driver = self::DRIVER_DOCTRINE_ORM)
    {
        parent::__construct(Controller::class, $bundlePath.'/Resources/config/resources', $driver);
    }
}
