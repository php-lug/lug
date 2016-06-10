<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Context;

use Lug\Bundle\GridBundle\Context\LocaleContext;
use Lug\Component\Grid\Context\LocaleContextInterface;
use Lug\Component\Locale\Context\LocaleContextInterface as BaseLocaleContextInterface;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|ComponentLocaleContextInterface
     */
    private $lugContext;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->lugContext = $this->createLocaleContextMock();
        $this->context = new LocaleContext($this->lugContext);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->context);
    }

    public function testLocale()
    {
        $this->lugContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue([$locale = 'fr', 'en']));

        $this->assertSame($locale, $this->context->getLocale());
    }

    /**
     * @expectedException \Lug\Bundle\GridBundle\Exception\LocaleNotFoundException
     * @expectedExceptionMessage The locale could not be found.
     */
    public function testLocaleMissing()
    {
        $this->lugContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue([]));

        $this->context->getLocale();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ComponentLocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->createMock(BaseLocaleContextInterface::class);
    }
}
