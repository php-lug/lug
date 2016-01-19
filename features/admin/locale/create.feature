# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Creating a locale
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to create a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | be   |

    Scenario: Navigating to the locale creation page
        Given I am on the dashboard page
        And I follow "Locales"
        And I follow "New"
        Then I should be on the locale creation page

    Scenario: Analyzing the locale creation page
        Given I am on the locale creation page
        Then I should see "Create a locale"
        And the "Code" field should contain ""
        And the "Enabled" checkbox should not be checked
        And the "Required" checkbox should not be checked
        But the option "Belgium" from the select "Code" should not exist

    Scenario: Missing locale fields
        Given I am on the locale creation page
        And I press "Submit"
        Then I should be on the locale creation page
        And I should see "The locale code should not be blank."

    Scenario: Creating a locale (minimal)
        Given I am on the locale creation page
        And I select "French" from "Code"
        And I press "Submit"
        Then I should be on the locale "fr" page
        And I should see "The locale \"fr\" has been created."
        And the locales should exist:
            | code | enabled | required |
            | fr   | no      | no       |

    Scenario: Creating a locale (full)
        Given I am on the locale creation page
        And I select "French" from "Code"
        And I check "Enabled"
        And I check "Required"
        And I press "Submit"
        Then I should be on the locale "fr" page
        And I should see "The locale \"fr\" has been created."
        And the locales should exist:
            | code | enabled | required |
            | fr   | yes     | yes      |
