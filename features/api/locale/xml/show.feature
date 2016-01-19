# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Showing a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to show a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
        And I set the header "Accept" with value "application/xml"

    Scenario: Analyzing a locale
        Given I send a "GET" request to "/locale/fr"
        Then the response should contain:
            """
                <locale>
                    <code>
                        <![CDATA[fr]]>
                    </code>
                    <enabled>true</enabled>
                    <required>false</required>
                    <created_at>
                        <![CDATA[2015-01-01T01:02:03+0100]]>
                    </created_at>
                    <updated_at>
                        <![CDATA[2015-02-02T02:03:04+0100]]>
                    </updated_at>
                </locale>
            """
