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

use Lug\Bundle\ResourceBundle\Form\Extension\DateTimeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateTimeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var DateTimeExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new DateTimeExtension();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(DateTimeType::class, $this->extension->getExtendedType());
    }

    public function testWidget()
    {
        $form = $this->factory
            ->create(DateTimeType::class)
            ->submit($datetime = '2015-01-01 01:02:03');

        $view = $form->createView();
        $data = $form->getData();

        $this->assertInstanceOf(\DateTimeInterface::class, $data);
        $this->assertSame($datetime, $data->format('Y-m-d H:i:s'));

        $this->assertArrayHasKey('widget', $view->vars);
        $this->assertSame('single_text', $view->vars['widget']);
    }
}
