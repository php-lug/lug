<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Negotiator;

use Lug\Component\Locale\Negotiator\LocaleNegotiator;
use Lug\Component\Locale\Negotiator\LocaleNegotiatorInterface;
use Negotiation\AcceptLanguage;
use Negotiation\LanguageNegotiator;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleNegotiatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleNegotiator
     */
    private $localeNegotiator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->localeNegotiator = new LocaleNegotiator();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleNegotiatorInterface::class, $this->localeNegotiator);
        $this->assertInstanceOf(LanguageNegotiator::class, $this->localeNegotiator);
    }

    public function testParse()
    {
        $fr = new AcceptLanguage('fr');
        $be = new AcceptLanguage('be;q=0.3');
        $en = new AcceptLanguage('en;q=0.1');
        $es = new AcceptLanguage('es');

        $this->assertEquals([$fr, $es, $be, $en], $this->localeNegotiator->parse('en;q=0.1,fr,be;q=0.3,es'));
    }

    /**
     * @expectedException \Negotiation\Exception\InvalidArgument
     * @expectedExceptionMessage The header string should not be empty.
     */
    public function testParseWithEmptyHeader()
    {
        $this->localeNegotiator->parse(' ');
    }
}
