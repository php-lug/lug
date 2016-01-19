<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source statusCode.
 */

namespace Lug\Component\Behat\Extension\Api\Matcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Matcher\XmlMatcher;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MatcherFactory extends SimpleFactory
{
    /**
     * {@inheritdoc}
     */
    protected function buildMatchers()
    {
        $matcher = new ArrayMatcher($this->buildScalarMatchers(), $this->buildParser());

        return new ChainMatcher([
            new JsonMatcher($matcher),
            new XmlMatcher($matcher),
        ]);
    }
}
