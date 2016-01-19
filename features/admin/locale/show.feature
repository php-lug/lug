# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Showing a locale
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to show a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |

    Scenario: Navigating to a locale page
        Given I am on the dashboard page
        And I follow "Locales"
        And I follow the locale grid show link "fr"
        Then I should be on the locale "fr" page

    Scenario: Analyzing a locale
        Given I am on the locale "fr" page
        Then I should see "Locale \"fr\""
