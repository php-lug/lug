<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Negotiator;

use Negotiation\AcceptLanguage;
use Negotiation\Exception\InvalidArgument;
use Negotiation\LanguageNegotiator;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleNegotiator extends LanguageNegotiator implements LocaleNegotiatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($header)
    {
        $header = trim($header);

        if (empty($header)) {
            throw new InvalidArgument('The header string should not be empty.');
        }

        preg_match_all('/(?:[^,"]*+(?:"[^"]*+")?)+[^,"]*+/', $header, $matches);

        return $this->sort(array_map(
            [$this, 'acceptFactory'],
            array_values(array_filter(array_map('trim', $matches[0])))
        ));
    }

    /**
     * @param AcceptLanguage[] $acceptLanguages
     *
     * @return AcceptLanguage[]
     */
    private function sort(array $acceptLanguages)
    {
        $topAcceptLanguages = [];
        $sortedAcceptLanguages = [];

        foreach ($acceptLanguages as $acceptLanguage) {
            if ($acceptLanguage->getQuality() === 1.0) {
                $topAcceptLanguages[] = $acceptLanguage;
            } else {
                $sortedAcceptLanguages[] = $acceptLanguage;
            }
        }

        usort($sortedAcceptLanguages, function (AcceptLanguage $a, AcceptLanguage $b) {
            return $a->getQuality() > $b->getQuality() ? -1 : 1;
        });

        return array_merge($topAcceptLanguages, $sortedAcceptLanguages);
    }
}
