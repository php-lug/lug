# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.json @lug.api.locale
Feature: Sorting locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to sort locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |

    Scenario Outline: Sorting the locales
        Given I send a "GET" request to "/locale?sorting=<sorting>"
        Then the response status code should be "200"
        And the "json" response should be sorted by "<property>" "<order>"

        Examples:
            | property   | sorting    | order |
            | code       | -code      | DESC  |
            | enabled    | enabled    | ASC   |
            | enabled    | -enabled   | DESC  |
            | required   | required   | ASC   |
            | required   | -required  | DESC  |
            | created_at | createdAt  | ASC   |
            | created_at | -createdAt | DESC  |
            | updated_at | updatedAt  | ASC   |
            | updated_at | -updatedAt | DESC  |
