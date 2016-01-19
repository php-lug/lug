# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Updating a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to update a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required |
            | fr   | yes     | yes      |
            | be   | no      | no       |
        And I set the header "Content-Type" with value "application/json"

    Scenario: Missing locale fields
        Given I send a "PUT" request to "/locale/fr"
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "code": 400,
                    "message": "Validation Failed",
                    "errors": {
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
                }
            """

    Scenario Outline: Invalid locale fields
        Given I send a "<method>" request to "/locale/fr" with body:
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
                    "code": 400,
                    "message": "Validation Failed",
                    "errors": {
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
                }
            """

        Examples:
            | method |
            | PUT    |
            | PATCH  |

    Scenario Outline: Duplicating locale code
        Given I send a "<method>" request to "/locale/fr" with body:
            """
              {
                  "code": "be",
                  "enabled": false,
                  "required": false
              }
            """
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "code": 400,
                    "message": "Validation Failed",
                    "errors": {
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
                }
            """

        Examples:
            | method |
            | PUT    |
            | PATCH  |

    Scenario Outline: Updating a locale
        Given I send a "<method>" request to "/locale/fr" with body:
            """
              {
                  "code": "es",
                  "enabled": false,
                  "required": false
              }
            """
        Then the response status code should be "204"
        And the response should be empty
        And the locales should exist:
            | code | enabled | required |
            | es   | no      | no       |
        And the locale "fr" should not exist

        Examples:
            | method |
            | PUT    |
            | PATCH  |

    Scenario: Patching a locale
        Given I send a "PATCH" request to "/locale/fr" with body:
            """
              {
                  "code": "es"
              }
            """
        Then the response status code should be "204"
        And the response should be empty
        And the locales should exist:
            | code | enabled | required |
            | es   | yes     | yes      |
        And the locale "fr" should not exist
