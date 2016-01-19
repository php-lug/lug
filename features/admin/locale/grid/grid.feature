# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Listing locales
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to list locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |

    Scenario: Navigating to the locale listing page
        Given I am on the dashboard page
        And I follow "Locales"
        Then I should be on the locale listing page

    Scenario: Analyzing the locale listing page
        Given I am on the locale listing page
        Then I should see "Browse locales"
        And the locale grid should be:
            | Code | Enabled | Required | Created at              | Updated at              |
            | be   | No      | No       | Mar 3, 2015 3:04:05 AM  | Apr 4, 2015 4:05:06 AM  |
            | en   | Yes     | Yes      | Jan 1, 2015 12:00:00 AM | Jan 1, 2015 12:00:01 AM |
            | fr   | Yes     | No       | Jan 1, 2015 1:02:03 AM  | Feb 2, 2015 2:03:04 AM  |
