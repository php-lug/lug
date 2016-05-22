# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Batching locales
    In order to reach visitors from multiple countries
    As a developer
    I should be able to batch locales

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | fr   |
            | be   |
        And I set the header "Content-Type" with value "application/xml"
        And I set the header "Accept" with value "application/xml"

    Scenario: Missing locales
        Given I send a "POST" request to "/locale/batch"
        Then the response status code should be "400"
        And the response should contain:
            """
                <form name="grid_batch">
                    <errors/>
                    <form name="all">
                        <errors/>
                    </form>
                    <form name="type">
                        <errors>
                            <entry>
                                <![CDATA[The grid batch should not be blank.]]>
                            </entry>
                        </errors>
                    </form>
                    <form name="value">
                        <errors>
                            <entry>
                                <![CDATA[You must select at least one locale.]]>
                            </entry>
                        </errors>
                    </form>
                </form>
            """

    Scenario: Batching default locale (delete)
        Given I send a "POST" request to "/locale/batch" with body:
            """
                <batch>
                    <type>delete</type>
                    <value>be</value>
                    <value>en</value>
                    <value>fr</value>
                </batch>
            """
        Then the response status code should be "409"
        And the response should contain:
            """
                <result>
                    <code>409</code>
                    <message>
                        <![CDATA[Conflict]]>
                    </message>
                </result>
            """
        And the locales should exist:
            | code |
            | en   |
            | fr   |
            | be   |

    Scenario: Batching default locale (all - delete)
        Given I send a "POST" request to "/locale/batch" with body:
            """
                <batch>
                    <type>delete</type>
                    <all>true</all>
                </batch>
            """
        Then the response status code should be "409"
        And the response should contain:
            """
                <result>
                    <code>409</code>
                    <message>
                        <![CDATA[Conflict]]>
                    </message>
                </result>
            """
        And the locales should exist:
            | code |
            | en   |
            | fr   |
            | be   |

    Scenario: Batching locales (delete)
        Given I send a "POST" request to "/locale/batch" with body:
            """
                <batch>
                    <type>delete</type>
                    <value>be</value>
                    <value>fr</value>
                </batch>
            """
        Then the response status code should be "204"
        And the response should be empty
        And the locales should not exist:
            | code |
            | fr   |
            | be   |
