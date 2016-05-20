<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\Tests\DependencyInjection\Compiler;

use Lug\Bundle\UiBundle\DependencyInjection\Compiler\RegisterMenuListenerPass;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterMenuListenerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterMenuListenerPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterMenuListenerPass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RegisterListenersPass::class, $this->compiler);
    }
}
