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

if [ "$UNIT_BUILD" = true ]; then
    bin/phpunit -c app
fi

if [ "$BDD_BUILD" = true ]; then
    bin/behat -p all
fi

if [ "$CS_BUILD" = true ]; then
    bin/php-cs-fixer fix --dry-run
fi
