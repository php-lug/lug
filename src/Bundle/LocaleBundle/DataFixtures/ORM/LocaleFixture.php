<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\DataFixtures\ORM;

use Lug\Bundle\LocaleBundle\DataFixtures\AbstractLocaleFixture;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleFixture extends AbstractLocaleFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getDriver()
    {
        return ResourceInterface::DRIVER_DOCTRINE_ORM;
    }
}
