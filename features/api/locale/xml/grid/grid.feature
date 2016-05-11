# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Listing locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to list locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required | createdAt           | updatedAt           |
            | fr   | yes     | no       | 2015-01-01 01:02:03 | 2015-02-02 02:03:04 |
            | be   | no      | no       | 2015-03-03 03:04:05 | 2015-04-04 04:05:06 |
        And I set the header "Accept" with value "application/xml"

    Scenario: Listing the locales
        Given I send a "GET" request to "/locale"
        Then the response status code should be "200"
        And the response should contain:
            """
                <result>
                    <entry>
                        <code><![CDATA[be]]></code>
                        <enabled>false</enabled>
                        <required>false</required>
                        <created_at><![CDATA[2015-03-03T03:04:05+0100]]></created_at>
                        <updated_at><![CDATA[2015-04-04T04:05:06+0200]]></updated_at>
                    </entry>
                    <entry>
                        <code><![CDATA[en]]></code>
                        <enabled>true</enabled>
                        <required>true</required>
                        <created_at><![CDATA[2015-01-01T00:00:00+0100]]></created_at>
                        <updated_at><![CDATA[2015-01-01T00:00:01+0100]]></updated_at>
                    </entry>
                    <entry>
                        <code><![CDATA[fr]]></code>
                        <enabled>true</enabled>
                        <required>false</required>
                        <created_at><![CDATA[2015-01-01T01:02:03+0100]]></created_at>
                        <updated_at><![CDATA[2015-02-02T02:03:04+0100]]></updated_at>
                    </entry>
                </result>
            """
