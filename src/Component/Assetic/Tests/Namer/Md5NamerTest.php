<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Tests\Namer;

use Assetic\Asset\AssetInterface;
use Lug\Component\Assetic\Namer\AbstractNamer;
use Lug\Component\Assetic\Namer\Md5Namer;
use Lug\Component\Assetic\Namer\NamerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Md5NamerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Md5Namer
     */
    private $md5Namer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->md5Namer = new Md5Namer();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(NamerInterface::class, $this->md5Namer);
        $this->assertInstanceOf(AbstractNamer::class, $this->md5Namer);
    }

    public function testName()
    {
        $this->assertSame(
            'b12242ca274fa3329773e864d7b3e1cb.txt',
            $this->md5Namer->name('/path/to/foo.txt', $this->createAssetMock())
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AssetInterface
     */
    private function createAssetMock()
    {
        return $this->getMock(AssetInterface::class);
    }
}
