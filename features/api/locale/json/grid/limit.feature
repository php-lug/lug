# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Limiting locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to limit locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | be   |

    Scenario: Limiting the locales (lower than min)
        Given I send a "GET" request to "/locale?limit=0"
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "code": 400,
                    "message": "Validation Failed",
                    "errors": {
                        "children": {
                            "filters": {
                                "children": {
                                    "code": {
                                        "children": {
                                            "type": {},
                                            "value": {}
                                        }
                                    },
                                    "enabled": {},
                                    "required": {},
                                    "createdAt": {
                                        "children": {
                                            "type": {},
                                            "value": {}
                                        }
                                    },
                                    "updatedAt": {
                                        "children": {
                                            "type": {},
                                            "value": {}
                                        }
                                    }
                                }
                            },
                            "sorting": {},
                            "page": {},
                            "limit": {
                                "errors": [
                                    "The grid limit should be upper or equal to 1."
                                ]
                            }
                        }
                    }
                }
            """

    Scenario: Limiting the locales (upper than max)
        Given I send a "GET" request to "/locale?limit=101"
        Then the response status code should be "400"
        And the response should contain:
            """
                {
                    "code": 400,
                    "message": "Validation Failed",
                    "errors": {
                        "children": {
                            "filters": {
                                "children": {
                                    "code": {
                                        "children": {
                                            "type": {},
                                            "value": {}
                                        }
                                    },
                                    "enabled": {},
                                    "required": {},
                                    "createdAt": {
                                        "children": {
                                            "type": {},
                                            "value": {}
                                        }
                                    },
                                    "updatedAt": {
                                        "children": {
                                            "type": {},
                                            "value": {}
                                        }
                                    }
                                }
                            },
                            "sorting": {},
                            "page": {},
                            "limit": {
                                "errors": [
                                    "The grid limit should be lower or equal to 100."
                                ]
                            }
                        }
                    }
                }
            """

    Scenario: Limiting the locales
        Given I send a "GET" request to "/locale?limit=1"
        Then the response status code should be "200"
        And the response should contain:
            """
                [
                    {
                        "code": "be",
                        "enabled": false,
                        "required": false,
                        "created_at": "@string@.isDateTime()",
                        "updated_at": "@string@.isDateTime()"
                    }
                ]
            """
