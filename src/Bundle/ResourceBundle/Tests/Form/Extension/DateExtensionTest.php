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

use Lug\Bundle\ResourceBundle\Form\Extension\DateExtension;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DateExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var DateExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new DateExtension();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtension($this->extension)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertSame(DateType::class, $this->extension->getExtendedType());
    }

    public function testWidget()
    {
        $form = $this->factory
            ->create(DateType::class)
            ->submit($date = '2015-01-01');

        $view = $form->createView();
        $data = $form->getData();

        $this->assertInstanceOf(\DateTimeInterface::class, $data);
        $this->assertSame($date, $data->format('Y-m-d'));

        $this->assertArrayHasKey('widget', $view->vars);
        $this->assertSame('single_text', $view->vars['widget']);
    }
}
