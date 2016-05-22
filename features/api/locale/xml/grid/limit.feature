# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Limiting locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to limit locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | be   |
        And I set the header "Accept" with value "application/xml"

    Scenario: Limiting the locales (lower than min)
        Given I send a "GET" request to "/locale?limit=0"
        Then the response status code should be "400"
        And the response should contain:
            """
                <form name="grid">
                    <errors/>
                    <form name="filters">
                        <errors/>
                        <form name="code">
                            <errors/>
                            <form name="type">
                                <errors/>
                            </form>
                            <form name="value">
                                <errors/>
                            </form>
                        </form>
                        <form name="enabled">
                            <errors/>
                        </form>
                        <form name="required">
                            <errors/>
                        </form>
                        <form name="createdAt">
                            <errors/>
                            <form name="type">
                                <errors/>
                            </form>
                            <form name="value">
                                <errors/>
                            </form>
                        </form>
                        <form name="updatedAt">
                            <errors/>
                            <form name="type">
                                <errors/>
                            </form>
                            <form name="value">
                                <errors/>
                            </form>
                        </form>
                    </form>
                    <form name="sorting">
                        <errors/>
                    </form>
                    <form name="page">
                        <errors/>
                    </form>
                    <form name="limit">
                        <errors>
                            <entry>
                                <![CDATA[The grid limit should be upper or equal to 1.]]>
                            </entry>
                        </errors>
                    </form>
                </form>
            """

    Scenario: Limiting the locales (upper than max)
        Given I send a "GET" request to "/locale?limit=101"
        Then the response status code should be "400"
        And the response should contain:
            """
                <form name="grid">
                    <errors/>
                    <form name="filters">
                        <errors/>
                        <form name="code">
                            <errors/>
                            <form name="type">
                                <errors/>
                            </form>
                            <form name="value">
                                <errors/>
                            </form>
                        </form>
                        <form name="enabled">
                            <errors/>
                        </form>
                        <form name="required">
                            <errors/>
                        </form>
                        <form name="createdAt">
                            <errors/>
                            <form name="type">
                                <errors/>
                            </form>
                            <form name="value">
                                <errors/>
                            </form>
                        </form>
                        <form name="updatedAt">
                            <errors/>
                            <form name="type">
                                <errors/>
                            </form>
                            <form name="value">
                                <errors/>
                            </form>
                        </form>
                    </form>
                    <form name="sorting">
                        <errors/>
                    </form>
                    <form name="page">
                        <errors/>
                    </form>
                    <form name="limit">
                        <errors>
                            <entry>
                                <![CDATA[The grid limit should be lower or equal to 100.]]>
                            </entry>
                        </errors>
                    </form>
                </form>
            """

    Scenario: Limiting the locales
        Given I send a "GET" request to "/locale?limit=1"
        Then the response status code should be "200"
        And the response should contain:
            """
                <result>
                    <entry>
                        <code><![CDATA[be]]></code>
                        <enabled>false</enabled>
                        <required>false</required>
                        <created_at><![CDATA[@string@.isDateTime()]]></created_at>
                        <updated_at><![CDATA[@string@.isDateTime()]]></updated_at>
                    </entry>
                </result>
            """
