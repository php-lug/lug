<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Tests\Context;

use Lug\Component\Grid\Context\LocaleContext;
use Lug\Component\Grid\Context\LocaleContextInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleContext
     */
    private $context;

    /**
     * @var string
     */
    private $locale;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->context = new LocaleContext($this->locale = 'fr');
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->context);
    }

    public function testLocale()
    {
        $this->assertSame($this->locale, $this->context->getLocale());
    }
}
