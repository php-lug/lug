# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Deleting a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to delete a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |
        And I set the header "Accept" with value "application/xml"

    Scenario: Deleting the default locale
        Given I send a "DELETE" request to "/locale/en"
        Then the response status code should be "409"
        And the response should contain:
            """
                <result xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <code>409</code>
                    <message>
                        <![CDATA[Conflict]]>
                    </message>
                    <errors xsi:nil="true"/>
                </result>
            """
        And the locale "en" should exist
