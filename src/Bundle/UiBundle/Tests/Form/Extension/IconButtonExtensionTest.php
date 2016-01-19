<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\UiBundle\Tests\Form\Extension;

use Lug\Bundle\UiBundle\Form\Extension\IconButtonExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class IconButtonExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension(new IconButtonExtension())
            ->getFormFactory();
    }

    public function testDefault()
    {
        $form = $this->factory->create(ButtonType::class);
        $view = $form->createView();

        $this->assertArrayHasKey('icon', $view->vars);
        $this->assertNull($view->vars['icon']);
    }

    public function testIcon()
    {
        $form = $this->factory->create(ButtonType::class, null, ['icon' => $icon = 'my.icon']);
        $view = $form->createView();

        $this->assertArrayHasKey('icon', $view->vars);
        $this->assertSame($icon, $view->vars['icon']);
    }
}
