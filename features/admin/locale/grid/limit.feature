# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Limiting locales
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to limit locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | be   |
        And I am on the locale listing page
        And I follow "Filters"
        And I wait the locale grid filters

    Scenario: Analyzing the locale listing page
        Then I should not see a pager

    Scenario: Limiting the locales (lower than min)
        Given I fill in "Limit" with "0"
        And I press "Filter"
        Then I should be on the locale listing page
        And I should not see a pager
        When I follow "Filters"
        And I wait the locale grid filters
        Then I should see "The grid limit should be upper or equal to 1."

    Scenario: Limiting the locales (upper than max)
        Given I fill in "Limit" with "101"
        And I press "Filter"
        Then I should be on the locale listing page
        And I should not see a pager
        When I follow "Filters"
        And I wait the locale grid filters
        Then I should see "The grid limit should be lower or equal to 100."

    Scenario: Limiting the locales
        Given I fill in "Limit" with "1"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid should be:
            | Code |
            | be   |
        And I should see a pager with "2" pages
