# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Batching locales
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to batch locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |
            | be   |

    Scenario: Analyzing the locale batching page
        Given I am on the locale listing page
        Then all locale grid batches should not be checked

    Scenario: Missing locales
        Given I am on the locale listing page
        And I press "Batch"
        Then I should be on the locale batching page
        And I should see "You must select at least one locale."

    @javascript
    Scenario: Checking batches
        Given I am on the locale listing page
        And I select the locale grid batch "en"
        When I toggle the locale grid batches
        Then all locale grid batches should be checked

    @javascript
    Scenario: Unchecking batches
        Given I am on the locale listing page
        And I select the locale grid batch "en"
        When I toggle the locale grid batches
        And I toggle the locale grid batches
        Then all locale grid batches should not be checked

    @javascript
    Scenario: Batching locales (delete)
        Given I am on the locale listing page
        And I select the locale grid batch "en"
        And I select the locale grid batch "fr"
        And I select the locale grid batch "be"
        And I press "Batch"
        Then I should be on the locale listing page
        And I should see "The default locale \"en\" can't be deleted."
        And I should see "The locale \"fr\" has been deleted."
        And I should see "The locale \"be\" has been deleted."
        And the locale "en" should exist
        And the locales should not exist:
            | code |
            | fr   |
            | be   |

    Scenario: Batching all locales (delete)
        Given I am on the locale listing page
        And I select the "all" locale grid batch
        And I press "Batch"
        Then I should be on the locale listing page
        And I should see "The default locale \"en\" can't be deleted."
        And I should see "The locale \"fr\" has been deleted."
        And I should see "The locale \"be\" has been deleted."
        And the locale "en" should exist
        And the locales should not exist:
            | code |
            | fr   |
            | be   |
