# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Sorting locales
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to sort locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |

    Scenario Outline: Sorting the locales
        Given I am on the locale listing page
        And I follow the locale grid sorting link "<column>" "<sorting>"
        Then I should be on the locale listing page
        And the locale grid column "<column>" should be sorted "<sorting>"

        Examples:
            | column     | sorting |
            | Code       | DESC    |
            | Enabled    | ASC     |
            | Enabled    | DESC    |
            | Required   | ASC     |
            | Required   | DESC    |
            | Created at | ASC     |
            | Created at | DESC    |
            | Updated at | ASC     |
            | Updated at | DESC    |
