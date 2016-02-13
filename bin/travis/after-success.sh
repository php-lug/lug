#!/usr/bin/env bash

# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

set -e

LUG_CACHE=${HOME}/.lug-cache
COVERAGE_BUILD=${COVERAGE_BUILD-false}

if [ "$COVERAGE_BUILD" = true ]; then
    OCULAR_PHAR=${LUG_CACHE}/ocular.phar

    if [ ! -f ${OCULAR_PHAR} ]; then
        curl https://scrutinizer-ci.com/ocular.phar > ${OCULAR_PHAR}
    fi

    php ${OCULAR_PHAR} code-coverage:upload --format=php-clover build/clover.xml
fi
