# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Creating a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to create a locale with the JSON format

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | be   |
        And I set the header "Content-Type" with value "application/json"

    Scenario: Missing locale fields
        Given I send a "POST" request to "/locale"
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "children": {
                        "code": {
                            "errors": [
                                "The locale code should not be blank."
                            ]
                        },
                        "enabled": {},
                        "required": {}
                    }
                }
            """

    Scenario: Invalid locale fields
        Given I send a "POST" request to "/locale" with body:
            """
              {
                  "code": "foo",
                  "enabled": "foo",
                  "required": "foo"
              }
            """
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "children": {
                        "code": {
                            "errors": [
                                "The locale code is not valid."
                            ]
                        },
                        "enabled": {
                            "errors": [
                                "The locale enabled flag is not valid."
                            ]
                        },
                        "required": {
                            "errors": [
                                "The locale required flag is not valid."
                            ]
                        }
                    }
                }
            """

    Scenario: Duplicating locale code
        Given I send a "POST" request to "/locale" with body:
            """
                {
                    "code": "be"
                }
            """
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "children": {
                        "code": {
                            "errors": [
                                "The locale code already exists."
                            ]
                        },
                        "enabled": {},
                        "required": {}
                    }
                }
            """

    Scenario: Creating a locale (minimal)
        Given I send a "POST" request to "/locale" with body:
            """
              {
                  "code": "fr"
              }
            """
        Then the response status code should be "201"
        And the response should contain:
            """
                {
                    "code": "fr",
                    "enabled": false,
                    "required": false,
                    "created_at": "@string@.isDateTime()",
                    "updated_at": "@string@.isDateTime()"
                }
            """
        And the locales should exist:
            | code | enabled | required |
            | fr   | no      | no       |

    Scenario: Creating a locale (full)
        Given I send a "POST" request to "/locale" with body:
            """
              {
                  "code": "fr",
                  "enabled": true,
                  "required": false
              }
            """
        Then the response status code should be "201"
        And the response should contain:
            """
                {
                    "code": "fr",
                    "enabled": true,
                    "required": false,
                    "created_at": "@string@.isDateTime()",
                    "updated_at": "@string@.isDateTime()"
                }
            """
        And the locales should exist:
            | code | enabled | required |
            | fr   | yes     | no       |
