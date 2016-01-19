# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Filtering locales
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to filter locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |
        And I am on the locale listing page
        And I follow "Filters"
        And I wait the locale grid filters

    Scenario: Analyzing the locale filtering page
        Given I should see "Filter locales"
        And the "Code -> Type" embed field should contain "Contains"
        And the "Code -> Value" embed field should contain ""
        And the "Enabled" field should contain ""
        And the "Required" field should contain ""
        And the "Created at -> Type" embed field should contain "Greater than or equals"
        And the "Created at -> Value" embed field should contain ""
        And the "Updated at -> Type" embed field should contain "Greater than or equals"
        And the "Updated at -> Value" embed field should contain ""

    Scenario: Empty filtering
        Given I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "be ; en ; fr"

    Scenario Outline: Filtering the locales by code (simple types)
        Given I select "<type>" from embed "Code -> Type"
        And I wait the locale grid filters refresh
        And I fill in embed "Code -> Value" with "<value>"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type            | value | cells   |
            | Contains        | f     | fr      |
            | Not contains    | e     | fr      |
            | Equals          | fr    | fr      |
            | Not equals      | fr    | be ; en |
            | Starts with     | f     | fr      |
            | Not starts with | f     | be ; en |
            | Ends with       | r     | fr      |
            | Not ends with   | r     | be ; en |

    @javascript
    Scenario Outline: Filtering the locales by code (empty types)
        Given I select "<type>" from embed "Code -> Type"
        And I wait the locale grid filters refresh
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type      | cells                 |
            | Empty     | There are no locales. |
            | Not empty | be ; en ; fr          |

    Scenario Outline: Filtering the locales by enabled
        Given I select "<enabled>" from "Enabled"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Enabled" should be "<cells>"

        Examples:
            | enabled | cells   |
            | Yes     | en ; fr |
            | No      | be      |

    Scenario Outline: Filtering the locales by required
        Given I select "<required>" from "Required"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Required" should be "<cells>"

        Examples:
            | required | cells   |
            | Yes      | en      |
            | No       | be ; fr |

    Scenario Outline: Filtering the locales by created at (simple types)
        Given I select "<type>" from embed "Created at -> Type"
        And I wait the locale grid filters refresh
        And I fill in embed "Created at -> Value" with "<value>"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type                   | value               | cells   |
            | Greater than or equals | 2015-01-01 01:02:03 | be ; fr |
            | Greater than           | 2015-01-01 01:02:03 | be      |
            | Less than or equals    | 2015-01-01 01:02:03 | en ; fr |
            | Less than              | 2015-01-01 01:02:03 | en      |
            | Equals                 | 2015-01-01 01:02:03 | fr      |
            | Not equals             | 2015-01-01 01:02:03 | be ; en |

    @javascript
    Scenario Outline: Filtering the locales by created at (compound types)
        Given I select "<type>" from embed "Created at -> Type"
        And I wait the locale grid filters refresh
        And I fill in embed "Created at -> From" with "<from>"
        And I fill in embed "Created at -> To" with "<to>"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type        | from                 | to                  | cells   |
            | Between     | 2015-01-01 01:02:03  | 2015-01-01 01:03:02 | fr      |
            | Not between | 2015-01-01 01:02:03  | 2015-01-01 01:03:02 | be ; en |

    @javascript
    Scenario Outline: Filtering the locales by created at (empty types)
        Given I select "<type>" from embed "Created at -> Type"
        And I wait the locale grid filters refresh
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type      | cells                 |
            | Empty     | There are no locales. |
            | Not empty | be ; en ; fr          |

    Scenario Outline: Filtering the locales by updated at (simple types)
        Given I select "<type>" from embed "Updated at -> Type"
        And I wait the locale grid filters refresh
        And I fill in embed "Updated at -> Value" with "<value>"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type                   | value               | cells   |
            | Greater than or equals | 2015-02-02 02:03:04 | be ; fr |
            | Greater than           | 2015-02-02 02:03:04 | be      |
            | Less than or equals    | 2015-02-02 02:03:04 | en ; fr |
            | Less than              | 2015-02-02 02:03:04 | en      |
            | Equals                 | 2015-02-02 02:03:04 | fr      |
            | Not equals             | 2015-02-02 02:03:04 | be ; en |

    @javascript
    Scenario Outline: Filtering the locales by updated at (compound types)
        Given I select "<type>" from embed "Updated at -> Type"
        And I wait the locale grid filters refresh
        And I fill in embed "Updated at -> From" with "<from>"
        And I fill in embed "Updated at -> To" with "<to>"
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type        | from                 | to                  | cells   |
            | Between     | 2015-02-02 02:03:04  | 2015-02-02 02:04:03 | fr      |
            | Not between | 2015-02-02 02:03:04  | 2015-02-02 02:04:03 | be ; en |

    @javascript
    Scenario Outline: Filtering the locales by updated at (empty types)
        Given I select "<type>" from embed "Updated at -> Type"
        And I wait the locale grid filters refresh
        And I press "Filter"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "<cells>"

        Examples:
            | type      | cells                 |
            | Empty     | There are no locales. |
            | Not empty | be ; en ; fr          |

    Scenario: Resetting the filters
        And I select "Yes" from "Enabled"
        And I press "Filter"
        And I follow "Filters"
        And I wait the locale grid filters
        And I press "Reset"
        Then I should be on the locale listing page
        And the locale grid for the column "Code" should be "be ; en ; fr"
