<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Model;

use Lug\Component\Locale\Model\Locale;
use Lug\Component\Locale\Model\LocaleInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->locale = new Locale();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LocaleInterface::class, $this->locale);
    }

    public function testDefaultState()
    {
        $this->assertNull($this->locale->getId());
        $this->assertNull($this->locale->getCode());
        $this->assertFalse($this->locale->isEnabled());
        $this->assertFalse($this->locale->isRequired());

        $this->assertInstanceOf(\DateTime::class, $this->locale->getCreatedAt());
        $this->assertGreaterThanOrEqual(new \DateTime('-1 second'), $this->locale->getCreatedAt());
        $this->assertLessThanOrEqual(new \DateTime(), $this->locale->getCreatedAt());

        $this->assertNull($this->locale->getUpdatedAt());
    }

    public function testId()
    {
        $this->locale->setId($id = 123);

        $this->assertSame($id, $this->locale->getId());
    }

    public function testCode()
    {
        $this->locale->setCode($code = 'fr');

        $this->assertSame($code, $this->locale->getCode());
    }

    public function testEnabled()
    {
        $this->locale->setEnabled(true);

        $this->assertTrue($this->locale->isEnabled());
    }

    public function testRequired()
    {
        $this->locale->setRequired(true);

        $this->assertTrue($this->locale->isRequired());
    }

    public function testCreatedAt()
    {
        $this->locale->setCreatedAt($createAt = new \DateTime());

        $this->assertSame($createAt, $this->locale->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $this->locale->setUpdatedAt($updatedAt = new \DateTime());

        $this->assertSame($updatedAt, $this->locale->getUpdatedAt());
    }
}
