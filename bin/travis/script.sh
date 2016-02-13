#!/usr/bin/env bash

# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

set -e

TRAVIS_PULL_REQUEST=${TRAVIS_PULL_REQUEST-false}
UNIT_BUILD=${UNIT_BUILD-false}
BDD_BUILD=${BDD_BUILD-false}
COVERAGE_BUILD=${COVERAGE_BUILD-false}

if [ "$UNIT_BUILD" = true ]; then
    vendor/bin/phpunit -c app `if [ "$COVERAGE_BUILD" = true ]; then echo "--coverage-clover build/clover.xml"; fi`

    if [ "$UNIT_BUILD" = true ] && [ "$TRAVIS_PULL_REQUEST" != false ]; then
        find src -maxdepth 3 -type f -name phpunit.xml.dist -printf "%h\n" \
            | parallel --gnu -j10% "cd {}; printf \"\\n\\n-> Testing {}\\n\\n\"; vendor/bin/phpunit"
    fi
fi

if [ "$BDD_BUILD" = true ]; then
    vendor/bin/behat -p all
fi
