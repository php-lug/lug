<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Filter\BaseCssFilter;
use Lug\Component\Assetic\Rewriter\CssRewriter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CssRewriteFilter extends BaseCssFilter
{
    /**
     * @var CssRewriter
     */
    private $cssRewriter;

    /**
     * @param CssRewriter $cssRewriter
     */
    public function __construct(CssRewriter $cssRewriter)
    {
        $this->cssRewriter = $cssRewriter;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($this->filterReferences($asset->getContent(), function ($reference) use ($asset) {
            try {
                return $this->cssRewriter->rewrite($reference, $asset);
            } catch (\Exception $e) {
                throw new FilterException($e->getMessage(), $e->getCode(), $e);
            }
        }));
    }
}
