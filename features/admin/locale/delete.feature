# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.locale
Feature: Deleting a locale
    In order to reach visitors from multiple countries
    As a administrator
    I should be able to delete a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |

    Scenario: Deleting the default locale
        Given I am on the locale listing page
        And I follow the locale grid deletion link "en"
        Then I should be on the locale listing page
        And I should see "The default locale \"en\" can't be deleted."
        And the locale "en" should exist

    Scenario: Deleting a locale
        Given I am on the locale listing page
        And I follow the locale grid deletion link "fr"
        Then I should be on the locale listing page
        And I should see "The locale \"fr\" has been deleted."
        And the locale "fr" should not exist
