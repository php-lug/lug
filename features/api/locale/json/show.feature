# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Showing a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to show a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |

    Scenario: Analyzing a locale
        Given I send a "GET" request to "/locale/fr"
        Then the response status code should be "200"
        Then the response should contain:
            """
                {
                    "code": "fr",
                    "enabled": true,
                    "required": false,
                    "created_at": "2015-01-01T01:02:03+0100",
                    "updated_at": "2015-02-02T02:03:04+0100"
                }
            """
