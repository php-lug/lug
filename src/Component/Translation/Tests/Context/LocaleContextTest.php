<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Tests\Provider;

use Lug\Component\Translation\Context\LocaleContext;
use Lug\Component\Translation\Context\LocaleContextInterface;

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
     * @var string
     */
    private $fallbackLocale;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->localeContext = new LocaleContext(
            $this->locales = ['fr_FR'],
            $this->fallbackLocale = 'en_EN'
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->localeContext);
    }

    public function testInitialState()
    {
        $this->assertSame($this->locales, $this->localeContext->getLocales());
        $this->assertSame($this->fallbackLocale, $this->localeContext->getFallbackLocale());
    }
}
