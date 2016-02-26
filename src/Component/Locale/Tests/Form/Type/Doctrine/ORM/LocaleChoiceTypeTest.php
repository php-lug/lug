<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Form\Type\Doctrine\ORM;

use Lug\Component\Locale\Form\Type\Doctrine\ORM\LocaleChoiceType;
use Lug\Component\Resource\Form\Type\Doctrine\ORM\AbstractResourceChoiceType;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleChoiceType
     */
    private $localeChoiceType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->localeChoiceType = new LocaleChoiceType($this->resource);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractResourceChoiceType::class, $this->localeChoiceType);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
