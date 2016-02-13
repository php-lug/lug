<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Registry;

use Lug\Component\Grid\Action\Type\TypeInterface;
use Lug\Component\Grid\Registry\ActionRegistry;
use Lug\Component\Registry\Model\Registry;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionRegistry
     */
    private $actionRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->actionRegistry = new ActionRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->actionRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->actionRegistry));
    }

    public function testInitialState()
    {
        $this->actionRegistry = new ActionRegistry([$key = 'foo' => $value = $this->createActionMock()]);

        $this->assertSame($value, $this->actionRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createActionMock()
    {
        return $this->getMock(TypeInterface::class);
    }
}
