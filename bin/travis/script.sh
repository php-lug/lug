#!/usr/bin/env bash

# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

UNIT_BUILD=${UNIT_BUILD-false}
BDD_BUILD=${BDD_BUILD-false}
CS_BUILD=${CS_BUILD-false}
COVERAGE_BUILD=${COVERAGE_BUILD-false}

if [ "$UNIT_BUILD" = true ]; then
    bin/phpunit -c app `if [ "$COVERAGE_BUILD" = true ]; then echo "--coverage-clover build/clover.xml"; fi`

    find src -maxdepth 3 -type f -name phpunit.xml.dist -printf "%h\n" \
        | parallel --gnu -j10% "cd {}; printf \"\\n\\n-> Testing {}\\n\\n\"; bin/phpunit"
fi

if [ "$BDD_BUILD" = true ]; then
    bin/behat -p all
fi

if [ "$CS_BUILD" = true ]; then
    bin/php-cs-fixer fix --dry-run
fi
