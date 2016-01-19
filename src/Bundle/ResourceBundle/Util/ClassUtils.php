<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Util;

use Doctrine\Common\Util\ClassUtils as DoctrineClassUtils;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ClassUtils extends DoctrineClassUtils
{
    /**
     * @param string $class
     *
     * @return null|string
     */
    public static function getRealNamespace($class)
    {
        if (($lastNsPos = strrpos($realClass = self::getRealClass($class), '\\')) !== false) {
            return substr($realClass, 0, $lastNsPos);
        }
    }

    /**
     * @param object $object
     *
     * @return null|string
     */
    public static function getNamespace($object)
    {
        return self::getRealNamespace(get_class($object));
    }
}
