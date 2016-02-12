<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Registry;

use Doctrine\Common\Persistence\ObjectManager;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ManagerRegistry extends Registry
{
    /**
     * @param ObjectManager[] $managers
     */
    public function __construct(array $managers = [])
    {
        parent::__construct(ObjectManager::class, $managers);
    }
}
