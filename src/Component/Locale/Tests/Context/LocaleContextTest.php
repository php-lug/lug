<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Context;

use Lug\Component\Locale\Context\LocaleContext;
use Lug\Component\Locale\Context\LocaleContextInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleContext
     */
    private $localeContext;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->localeContext = new LocaleContext($this->locales = ['fr_FR']);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->localeContext);
    }

    public function testLocales()
    {
        $this->assertSame($this->locales, $this->localeContext->getLocales());
    }
}
