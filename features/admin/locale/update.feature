# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Updating a locale
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to update a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required |
            | fr   | yes     | yes      |

    Scenario: Navigating to the locale edition page
        Given I am on the dashboard page
        And I follow "Locales"
        And I follow the locale grid edition link "fr"
        Then I should be on the locale "fr" edition page

    Scenario: Browsing the locale edition page
        Given I am on the locale "fr" edition page
        Then I should see "Edit the locale \"fr\""
        And the "Code" field should contain "fr"
        And the "Enabled" checkbox should be checked
        And the "Required" checkbox should be checked
        But the option "Belgium" from the select "Code" should not exist

    Scenario: Missing locale fields
        Given I am on the locale "fr" edition page
        And I select "" from "Code"
        And I press "Submit"
        Then I should be on the locale "fr" edition page
        And I should see "The locale code should not be blank."

    Scenario: Updating a locale
        Given I am on the locale "fr" edition page
        And I select "Spanish" from "Code"
        And I uncheck "Enabled"
        And I uncheck "Required"
        And I press "Submit"
        Then I should be on the locale "es" page
        And I should see "The locale \"es\" has been updated."
        And the locales should exist:
            | code | enabled | required |
            | es   | no      | no       |
        And the locale "fr" should not exist
