#!/usr/bin/env bash

# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

COVERAGE_BUILD=${COVERAGE_BUILD-false}

if [ "$COVERAGE_BUILD" = true ];
    then wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover build/coverage.xml
fi
