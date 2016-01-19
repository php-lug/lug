# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Batching locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to batch locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |
            | be   |
        And I set the header "Content-Type" with value "application/json"

    Scenario: Missing locales
        Given I send a "POST" request to "/locale/batch"
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "code": 400,
                    "message": "Validation Failed",
                    "errors": {
                        "children": {
                            "type": {
                                "errors": [
                                    "The grid batch should not be blank."
                                ]
                            },
                            "value": {
                                "errors": [
                                    "You must select at least one locale."
                                ]
                            }
                        }
                    }
                }
            """

    Scenario: Batching default locale (delete)
        Given I send a "POST" request to "/locale/batch" with body:
            """
                {
                    "type": "delete",
                    "value": ["be", "en", "fr"]
                }
            """
        Then the response status code should be "409"
        And the response should contain:
            """
                {
                    "code": 409,
                    "message": "Conflict"
                }
            """
        And the locales should exist:
            | code |
            | en   |
            | fr   |
            | be   |

    Scenario: Batching locales (delete)
        Given I send a "POST" request to "/locale/batch" with body:
            """
                {
                    "type": "delete",
                    "value": ["be", "fr"]
                }
            """
        Then the response status code should be "204"
        And the response should be empty
        And the locales should not exist:
            | code |
            | fr   |
            | be   |
