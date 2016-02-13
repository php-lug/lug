#!/usr/bin/env bash

# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

set -e

TRAVIS_PHP_VERSION=${TRAVIS_PHP_VERSION-unknown}
TRAVIS_PULL_REQUEST=${TRAVIS_PULL_REQUEST-false}
LUG_CACHE=${HOME}/.lug-cache
LUG_DRIVER=${LUG_DRIVER-unknown}
MONGODB_BUILD=${MONGODB_BUILD-false}
UNIT_BUILD=${UNIT_BUILD-false}
BDD_BUILD=${BDD_BUILD-false}
COVERAGE_BUILD=${COVERAGE_BUILD-false}
DISPLAY=${DISPLAY-:99}

if [ ! -d ${LUG_CACHE} ]; then
    mkdir -p ${LUG_CACHE}
fi

if [ "$TRAVIS_PHP_VERSION" = "hhvm" ]; then
    PHP_INI=/etc/hhvm/php.ini
else
    PHP_INI=~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
fi

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
    composer remove --dev --no-update doctrine/mongodb-odm-bundle
fi

if [ "$UNIT_BUILD" != true ] && [ "$BDD_BUILD" != true ]; then
    composer remove --dev --no-update phpunit/phpunit
    composer remove --dev --no-update symfony/phpunit-bridge
fi

if [ "$BDD_BUILD" = true ]; then
    export DISPLAY=${DISPLAY}
    /sbin/start-stop-daemon -Sbmq -p /tmp/xvfb_99.pid -x /usr/bin/Xvfb -- ${DISPLAY} -ac -screen 0, 1600x1200x24

    SELENIUM_JAR=${LUG_CACHE}/selenium.jar
    CHROME_DRIVER_ZIP=${LUG_CACHE}/chromedriver.zip
    CHROME_DRIVER=${LUG_CACHE}/chromedriver

    if [ ! -f ${SELENIUM_JAR} ]; then
        curl http://selenium-release.storage.googleapis.com/2.48/selenium-server-standalone-2.48.0.jar > ${SELENIUM_JAR}
    fi

    if [ ! -f ${CHROME_DRIVER} ]; then
        curl http://chromedriver.storage.googleapis.com/2.12/chromedriver_linux64.zip > ${CHROME_DRIVER_ZIP}
        unzip ${CHROME_DRIVER_ZIP} -d ${LUG_CACHE}
        rm ${CHROME_DRIVER_ZIP}
    fi

    java -jar ${SELENIUM_JAR} -Dwebdriver.chrome.driver=${CHROME_DRIVER} > /dev/null 2>&1 &

    printf "\nalways_populate_raw_post_data = -1" >> ${PHP_INI}

    if [ "$LUG_DRIVER" != "doctrine/orm" ]; then
        composer remove --dev --no-update doctrine/orm
        composer remove --dev --no-update doctrine/doctrine-bundle
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

composer install --prefer-source

if [ "$UNIT_BUILD" = true ] && [ "$TRAVIS_PULL_REQUEST" != false ]; then
    find src -maxdepth 3 -type f -name phpunit.xml.dist -printf "%h\n" \
        | parallel --gnu -j10% "cd {}; printf \"\\n\\n-> Installing {}\\n\\n\"; composer install --prefer-source"
fi

if [ "$BDD_BUILD" = true ]; then
    npm install

    GULP_ENV=prod
    node_modules/.bin/gulp

    php app/console server:run 127.0.0.1:8080 > /dev/null 2>&1 &

    if [ "$LUG_DRIVER" = "doctrine/orm" ]; then
        php app/console doctrine:schema:update --force
    fi

    if [ "$LUG_DRIVER" = "doctrine/mongodb" ]; then
        php app/console doctrine:mongodb:schema:create
    fi
fi
