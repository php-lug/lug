<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Registry;

use Lug\Bundle\GridBundle\Registry\FormTypeRegistry;
use Lug\Component\Registry\Model\Registry;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormTypeRegistry
     */
    private $formTypeRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formTypeRegistry = new FormTypeRegistry();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Registry::class, $this->formTypeRegistry);
    }

    public function testDefaultState()
    {
        $this->assertEmpty(iterator_to_array($this->formTypeRegistry));
    }

    public function testInitialState()
    {
        $this->formTypeRegistry = new FormTypeRegistry([$key = 'foo' => $value = $this->createFormTypeMock()]);

        $this->assertSame($value, $this->formTypeRegistry[$key]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeInterface
     */
    private function createFormTypeMock()
    {
        return $this->getMock(FormTypeInterface::class);
    }
}
