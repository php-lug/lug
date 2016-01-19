# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Listing locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to list locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |

    Scenario: Listing the locales
        Given I send a "GET" request to "/locale"
        Then the response should contain:
            """
                [
                    {
                        "code": "be",
                        "enabled": false,
                        "required": false,
                        "created_at": "2015-03-03T03:04:05+0100",
                        "updated_at": "2015-04-04T04:05:06+0200"
                    },
                    {
                        "code": "en",
                        "enabled": true,
                        "required": true,
                        "created_at": "2015-01-01T00:00:00+0100",
                        "updated_at": "2015-01-01T00:00:01+0100"
                    },
                    {
                        "code": "fr",
                        "enabled": true,
                        "required": false,
                        "created_at": "2015-01-01T01:02:03+0100",
                        "updated_at": "2015-02-02T02:03:04+0100"
                    }
                ]
            """
