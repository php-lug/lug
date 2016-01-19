<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Tests\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Filter\FilterInterface;
use Lug\Component\Assetic\Filter\CssRewriteFilter;
use Lug\Component\Assetic\Rewriter\CssRewriter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CssRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CssRewriteFilter
     */
    private $cssRewriteFilter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CssRewriter
     */
    private $cssRewriter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cssRewriter = $this->createCssRewriterMock();
        $this->cssRewriteFilter = new CssRewriteFilter($this->cssRewriter);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(FilterInterface::class, $this->cssRewriteFilter);
    }

    public function testFilterLoad()
    {
        $this->cssRewriter
            ->expects($this->never())
            ->method('rewrite');

        $this->cssRewriteFilter->filterLoad($this->createAssetMock());
    }

    public function testFilterDump()
    {
        $asset = $this->createAssetMock();

        $this->cssRewriter
            ->expects($this->once())
            ->method('rewrite')
            ->with(
                $this->identicalTo($reference = $this->createReference()),
                $this->identicalTo($asset)
            )
            ->will($this->returnValue($rewrite = 'rewritten'));

        $asset
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($reference[0]));

        $asset
            ->expects($this->once())
            ->method('setContent')
            ->with($this->identicalTo('rewritten'));

        $this->cssRewriteFilter->filterDump($asset);
    }

    public function testFilterDumpException()
    {
        $asset = $this->createAssetMock();
        $exception = new \Exception('expected message', 1234);

        $this->cssRewriter
            ->expects($this->once())
            ->method('rewrite')
            ->with(
                $this->identicalTo($reference = $this->createReference()),
                $this->identicalTo($asset)
            )
            ->will($this->throwException($exception));

        $asset
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($reference[0]));

        try {
            $this->cssRewriteFilter->filterDump($asset);
            $this->fail();
        } catch (FilterException $e) {
            $this->assertSame($e->getMessage(), $exception->getMessage());
            $this->assertSame($e->getCode(), $exception->getCode());
            $this->assertSame($e->getPrevious(), $exception);
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CssRewriter
     */
    private function createCssRewriterMock()
    {
        return $this->getMockBuilder(CssRewriter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AssetInterface
     */
    private function createAssetMock()
    {
        return $this->getMock(AssetInterface::class);
    }

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function createReference($path = 'foo.jpg')
    {
        return [
            $url = 'url("'.($path).'")',
            '"',
            'url' => $path,
            $path,
            '"',
        ];
    }
}
