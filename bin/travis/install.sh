#!/usr/bin/env bash

# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

TRAVIS_PHP_VERSION=${TRAVIS_PHP_VERSION-unknown}
LUG_DRIVER=${LUG_DRIVER-unknown}
MONGODB_BUILD=${MONGODB_BUILD-false}
UNIT_BUILD=${UNIT_BUILD-false}
BDD_BUILD=${BDD_BUILD-false}
CS_BUILD=${CS_BUILD-false}
COVERAGE_BUILD=${COVERAGE_BUILD-false}
DISPLAY=${DISPLAY-:99}
PHP_INI=~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini

mysql -e "CREATE DATABASE lug_test;"
printf "\ndate.timezone = Europe/Paris" >> ${PHP_INI}

if [ "$TRAVIS_PHP_VERSION" != "hhvm" ] && [ "$COVERAGE_BUILD" != true ]; then
    phpenv config-rm xdebug.ini
fi

if [ "$COVERAGE_BUILD" = true ]; then
    printf "\nmemory_limit = -1" >> ${PHP_INI}
fi

composer self-update

if [ "$MONGODB_BUILD" = true ]; then
    printf "\nextension = mongo.so" >> ${PHP_INI}
else
    composer remove --no-update doctrine/mongodb-odm-bundle
fi

if [ "$UNIT_BUILD" != true ] && [ "$BDD_BUILD" != true ]; then
    composer remove --dev --no-update phpunit/phpunit
    composer remove --dev --no-update symfony/phpunit-bridge
fi

if [ "$BDD_BUILD" = true ]; then
    export DISPLAY=${DISPLAY}
    /sbin/start-stop-daemon -Sbmq -p /tmp/xvfb_99.pid -x /usr/bin/Xvfb -- ${DISPLAY} -ac -screen 0, 1600x1200x24

    curl http://selenium-release.storage.googleapis.com/2.48/selenium-server-standalone-2.48.0.jar > selenium.jar
    curl http://chromedriver.storage.googleapis.com/2.12/chromedriver_linux64.zip > chromedriver.zip
    unzip chromedriver.zip

    java -jar selenium.jar -Dwebdriver.chrome.driver=./chromedriver > /dev/null 2>&1 &

    printf "\nalways_populate_raw_post_data = -1" >> ${PHP_INI}

    if [ "$LUG_DRIVER" != "doctrine/orm" ]; then
        composer remove --no-update doctrine/dbal
        composer remove --no-update doctrine/orm
        composer remove --no-update doctrine/doctrine-bundle
        composer remove --dev --no-update doctrine/doctrine-fixtures-bundle
    fi

    if [ "$LUG_DRIVER" = "doctrine/mongodb" ]; then
        composer require --dev --no-update doctrine/data-fixtures
    fi
else
    composer remove --dev --no-update behat/symfony2-extension
    composer remove --dev --no-update behat/mink-extension
    composer remove --dev --no-update behat/mink-browserkit-driver
    composer remove --dev --no-update behat/mink-selenium2-driver
fi

if [ "$CS_BUILD" != true ]; then
    composer remove --dev --no-update fabpot/php-cs-fixer
fi

npm install -g bower
composer install --prefer-source

if [ "$UNIT_BUILD" = true ]; then
    find src -maxdepth 3 -type f -name phpunit.xml.dist -printf "%h\n" \
        | parallel --gnu -j10% "cd {}; printf \"\\n\\n-> Installing {}\\n\\n\"; composer install --prefer-source"
fi

if [ "$BDD_BUILD" = true ]; then
    php app/console server:run 127.0.0.1:8080 > /dev/null 2>&1 &
    php app/console assetic:dump

    if [ "$LUG_DRIVER" = "doctrine/orm" ]; then
        php app/console doctrine:schema:update --force
    fi

    if [ "$LUG_DRIVER" = "doctrine/mongodb" ]; then
        php app/console doctrine:mongodb:schema:create
    fi
fi
