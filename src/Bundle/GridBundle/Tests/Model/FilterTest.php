<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Model;

use Lug\Bundle\GridBundle\Model\Filter;
use Lug\Bundle\GridBundle\Model\FilterInterface;
use Lug\Component\Grid\Model\Filter as BaseFilter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $form;

    /**
     * @var mixed[]
     */
    private $formOptions;

    /**
     * @var mixed[]
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->name = 'name';
        $this->label = 'label';
        $this->type = 'type';
        $this->form = 'form';
        $this->formOptions = ['foo' => 'bar'];
        $this->options = ['baz' => 'bat'];

        $this->filter = new Filter(
            $this->name,
            $this->label,
            $this->type,
            $this->form,
            $this->formOptions,
            $this->options
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
        $this->assertInstanceOf(BaseFilter::class, $this->filter);
    }

    public function testDefaultState()
    {
        $this->filter = new Filter($this->name, $this->label, $this->type, $this->form);

        $this->assertSame($this->name, $this->filter->getName());
        $this->assertSame($this->label, $this->filter->getLabel());
        $this->assertSame($this->type, $this->filter->getType());
        $this->assertSame($this->form, $this->filter->getForm());
        $this->assertFalse($this->filter->hasFormOptions());
        $this->assertEmpty($this->filter->getFormOptions());
        $this->assertFalse($this->filter->hasOptions());
        $this->assertEmpty($this->filter->getOptions());
        $this->assertFalse($this->filter->hasOption('foo'));
    }

    public function testInitialState()
    {
        $this->assertSame($this->name, $this->filter->getName());
        $this->assertSame($this->label, $this->filter->getLabel());
        $this->assertSame($this->type, $this->filter->getType());
        $this->assertSame($this->form, $this->filter->getForm());

        $this->assertTrue($this->filter->hasFormOptions());
        $this->assertSame($this->formOptions, $this->filter->getFormOptions());
        $this->assertTrue($this->filter->hasFormOption('foo'));
        $this->assertSame('bar', $this->filter->getFormOption('foo'));

        $this->assertTrue($this->filter->hasOptions());
        $this->assertSame($this->options, $this->filter->getOptions());
        $this->assertTrue($this->filter->hasOption('baz'));
        $this->assertSame('bat', $this->filter->getOption('baz'));
    }

    /**
     * @expectedException \Lug\Component\Grid\Exception\OptionNotFoundException
     * @expectedExceptionMessage The filter form option "bar" could not be found.
     */
    public function testMissingFormOption()
    {
        $this->filter->getFormOption('bar');
    }
}
