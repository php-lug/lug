# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.locale
Feature: Deleting a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to delete a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |

    Scenario: Deleting a locale
        Given I send a "DELETE" request to "/locale/fr"
        Then the response status code should be "204"
        And the response should be empty
        And the locale "fr" should not exist
