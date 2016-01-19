<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AdminBundle\Menu;

use Lug\Bundle\UiBundle\Menu\AbstractMenuBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SidebarMenuBuilder extends AbstractMenuBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lug.admin.sidebar';
    }
}
