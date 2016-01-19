# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Paginating locales
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to paginate locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |
            | be   |
        And I am on the locale listing page
        And I follow "Filters"
        And I wait the locale grid filters
        And I fill in "Limit" with "1"
        And I press "Filter"

    Scenario: Analyzing the locale listing page
        Then the locale grid should be:
            | Code |
            | be   |
        And I should see a pager with "3" pages

    Scenario: Following the next page
        Given I follow the page "Next"
        Then I should be on the locale listing page
        And the locale grid should be:
            | Code |
            | en   |

    Scenario: Following a page
        Given I follow the page "3"
        Then I should be on the locale listing page
        And the locale grid should be:
            | Code |
            | fr   |

    Scenario: Following the previous page
        Given I follow the page "3"
        And I follow the page "Previous"
        Then I should be on the locale listing page
        And the locale grid should be:
            | Code |
            | en   |
