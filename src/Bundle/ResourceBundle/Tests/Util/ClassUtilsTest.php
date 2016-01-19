<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Util;

use Lug\Bundle\ResourceBundle\Util\ClassUtils;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ClassUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider realNamespaceProvider
     */
    public function testRealNamespace($class, $namespace)
    {
        $this->assertSame($namespace, ClassUtils::getRealNamespace($class));
    }

    /**
     * @dataProvider namespaceProvider
     */
    public function testNamespace($object, $namespace)
    {
        $this->assertSame($namespace, ClassUtils::getNamespace($object));
    }

    public function realNamespaceProvider()
    {
        return [
            [ClassUtils::class, 'Lug\\Bundle\\ResourceBundle\\Util'],
            [\stdClass::class, null],
        ];
    }

    public function namespaceProvider()
    {
        return [
            [new ClassUtils(), 'Lug\\Bundle\\ResourceBundle\\Util'],
            [new \stdClass(), null],
        ];
    }
}
