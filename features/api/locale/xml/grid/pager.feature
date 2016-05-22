# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Paginating locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to paginate locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |
            | be   |
        And I set the header "Accept" with value "application/xml"

    Scenario: Getting an invalid page
        Given I send a "GET" request to "/locale?limit=1&page=0"
        Then the response status code should be "404"
        And the response should contain:
            """
                <result>
                    <code>404</code>
                    <message>
                        <![CDATA[Not Found]]>
                    </message>
                </result>
            """

    Scenario: Get a page
        Given I send a "GET" request to "/locale?limit=1&page=3"
        Then the response status code should be "200"
        And the response should contain:
            """
                <result>
                    <entry>
                        <code><![CDATA[fr]]></code>
                        <enabled>false</enabled>
                        <required>false</required>
                        <created_at><![CDATA[@string@.isDateTime()]]></created_at>
                        <updated_at><![CDATA[@string@.isDateTime()]]></updated_at>
                    </entry>
                </result>
            """
