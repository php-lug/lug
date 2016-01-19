# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.admin @lug.admin.dashboard
Feature: Browse the dashboard
    In order to have an overview of the administration
    As an administrator
    I should be able to browse the dashboard

    Background:
        Given the application has its default locale configured

    Scenario: Navigating to the dashboard
        Given I am on the dashboard page
        And I follow "Lug"
        Then I should be on the dashboard page

    Scenario: Browsing the dashboard
        Given I am on the admin page
        Then I should be on the dashboard page
        And I should see "Dashboard"
