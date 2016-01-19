# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Filtering locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to filter locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |
        And I set the header "Accept" with value "application/xml"

    Scenario Outline: Filtering the locales by code (simple types)
        Given I send a "GET" request to "/locale?filters[code][type]=<type>&filters[code][value]=<value>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type            | value | codes   |
            | contains        | f     | fr      |
            | not_contains    | e     | fr      |
            | equals          | fr    | fr      |
            | not_equals      | fr    | be ; en |
            | starts_with     | f     | fr      |
            | not_starts_with | f     | be ; en |
            | ends_with       | r     | fr      |
            | not_ends_with   | r     | be ; en |

    Scenario Outline: Filtering the locales by code (empty types)
        Given I send a "GET" request to "/locale?filters[code][type]=<type>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type      | codes        |
            | empty     |              |
            | not_empty | be ; en ; fr |

    Scenario Outline: Filtering the locales by enabled
        Given I send a "GET" request to "/locale?filters[enabled]=<enabled>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | enabled | codes   |
            | yes     | en ; fr |
            | no      | be      |

    Scenario Outline: Filtering the locales by required
        Given I send a "GET" request to "/locale?filters[required]=<required>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | required | codes   |
            | yes      | en      |
            | no       | be ; fr |

    Scenario Outline: Filtering the locales by created at (simple types)
        Given I send a "GET" request to "/locale?filters[createdAt][type]=<type>&filters[createdAt][value]=<value>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type                   | value               | codes   |
            | greater_than_or_equals | 2015-01-01 01:02:03 | be ; fr |
            | greater_than           | 2015-01-01 01:02:03 | be      |
            | less_than_or_equals    | 2015-01-01 01:02:03 | en ; fr |
            | less_than              | 2015-01-01 01:02:03 | en      |
            | equals                 | 2015-01-01 01:02:03 | fr      |
            | not_equals             | 2015-01-01 01:02:03 | be ; en |

    Scenario Outline: Filtering the locales by created at (compound types)
        Given I send a "GET" request to "/locale?filters[createdAt][type]=<type>&filters[createdAt][from]=<from>&filters[createdAt][to]=<to>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type        | from                 | to                  | codes   |
            | between     | 2015-01-01 01:02:03  | 2015-01-01 01:03:02 | fr      |
            | not_between | 2015-01-01 01:02:03  | 2015-01-01 01:03:02 | be ; en |

    Scenario Outline: Filtering the locales by created at (empty types)
        Given I send a "GET" request to "/locale?filters[createdAt][type]=<type>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type      | codes        |
            | empty     |              |
            | not_empty | be ; en ; fr |

    Scenario Outline: Filtering the locales by updated at (simple types)
        Given I send a "GET" request to "/locale?filters[updatedAt][type]=<type>&filters[updatedAt][value]=<value>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type                   | value               | codes   |
            | greater_than_or_equals | 2015-02-02 02:03:04 | be ; fr |
            | greater_than           | 2015-02-02 02:03:04 | be      |
            | less_than_or_equals    | 2015-02-02 02:03:04 | en ; fr |
            | less_than              | 2015-02-02 02:03:04 | en      |
            | equals                 | 2015-02-02 02:03:04 | fr      |
            | not_equals             | 2015-02-02 02:03:04 | be ; en |

    Scenario Outline: Filtering the locales by updated at (compound types)
        Given I send a "GET" request to "/locale?filters[updatedAt][type]=<type>&filters[updatedAt][from]=<from>&filters[updatedAt][to]=<to>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type        | from                 | to                  | codes   |
            | between     | 2015-02-02 02:03:04  | 2015-02-02 02:04:03 | fr      |
            | not_between | 2015-02-02 02:03:04  | 2015-02-02 02:04:03 | be ; en |

    Scenario Outline: Filtering the locales by updated at (empty types)
        Given I send a "GET" request to "/locale?filters[updatedAt][type]=<type>"
        Then the response status code should be "200"
        And the "xml" response should be filtered by "code" "<codes>"

        Examples:
            | type      | codes        |
            | empty     |              |
            | not_empty | be ; en ; fr |
