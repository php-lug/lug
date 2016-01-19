<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AsseticBundle\Tests;

use Lug\Bundle\AsseticBundle\LugAsseticBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LugAsseticBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugAsseticBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new LugAsseticBundle();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }
}
