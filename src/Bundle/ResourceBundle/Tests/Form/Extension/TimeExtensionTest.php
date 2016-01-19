<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Form\Extension;

use Lug\Bundle\ResourceBundle\Form\Extension\TimeExtension;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var TimeExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new TimeExtension();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(TimeType::class, $this->extension->getExtendedType());
    }

    public function testWidget()
    {
        $form = $this->factory
            ->create(TimeType::class)
            ->submit($time = '01:02:03');

        $view = $form->createView();
        $data = $form->getData();

        $this->assertInstanceOf(\DateTimeInterface::class, $data);
        $this->assertSame($time, $data->format('H:i:s'));

        $this->assertArrayHasKey('widget', $view->vars);
        $this->assertSame('single_text', $view->vars['widget']);

        $this->assertArrayHasKey('attr', $view->vars);
        $this->assertArrayHasKey('step', $view->vars['attr']);
        $this->assertSame(1, $view->vars['attr']['step']);
    }
}
