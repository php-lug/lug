# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Creating a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to create a locale with the XML format

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code |
            | be   |
        And I set the header "Content-Type" with value "application/xml"
        And I set the header "Accept" with value "application/xml"

    Scenario: Missing locale fields
        Given I send a "POST" request to "/locale"
        Then the response status code should be "400"
        And the response should contain:
            """
                <form name="lug_locale">
                    <errors/>
                    <form name="code">
                        <errors>
                            <entry>
                                <![CDATA[The locale code should not be blank.]]>
                            </entry>
                        </errors>
                    </form>
                    <form name="enabled">
                        <errors/>
                    </form>
                    <form name="required">
                        <errors/>
                    </form>
                </form>
            """

    Scenario: Invalid locale fields
        Given I send a "POST" request to "/locale" with body:
            """
                <locale>
                    <code>foo</code>
                    <enabled>foo</enabled>
                    <required>foo</required>
                </locale>
            """
        Then the response status code should be "400"
        And the response should contain:
            """
                <form name="lug_locale">
                    <errors/>
                    <form name="code">
                        <errors>
                            <entry>
                                <![CDATA[The locale code is not valid.]]>
                            </entry>
                        </errors>
                    </form>
                    <form name="enabled">
                        <errors>
                            <entry>
                                <![CDATA[The locale enabled flag is not valid.]]>
                            </entry>
                        </errors>
                    </form>
                    <form name="required">
                        <errors>
                            <entry>
                                <![CDATA[The locale required flag is not valid.]]>
                            </entry>
                        </errors>
                    </form>
                </form>
            """

    Scenario: Duplicating locale code
        Given I send a "POST" request to "/locale" with body:
            """
                <locale>
                    <code>be</code>
                </locale>
            """
        Then the response status code should be "400"
        And the response should contain:
            """
                <form name="lug_locale">
                    <errors/>
                    <form name="code">
                        <errors>
                            <entry>
                                <![CDATA[The locale code already exists.]]>
                            </entry>
                        </errors>
                    </form>
                    <form name="enabled">
                        <errors/>
                    </form>
                    <form name="required">
                        <errors/>
                    </form>
                </form>
            """

    Scenario: Creating a locale (minimal)
        Given I send a "POST" request to "/locale" with body:
            """
                <locale>
                    <code>fr</code>
                </locale>
            """
        Then the response status code should be "201"
        And the response should contain:
            """
                <locale>
                    <code>
                        <![CDATA[fr]]>
                    </code>
                    <enabled>false</enabled>
                    <required>false</required>
                    <created_at>
                        <![CDATA[@string@.isDateTime()]]>
                    </created_at>
                    <updated_at>
                        <![CDATA[@string@.isDateTime()]]>
                    </updated_at>
                </locale>
            """
        And the locales should exist:
            | code | enabled | required |
            | fr   | no      | no       |

    Scenario: Creating a locale (full)
        Given I send a "POST" request to "/locale" with body:
            """
                <locale>
                    <code>fr</code>
                    <enabled>true</enabled>
                    <required>false</required>
                </locale>
            """
        Then the response status code should be "201"
        And the response should contain:
            """
                <locale>
                    <code>
                        <![CDATA[fr]]>
                    </code>
                    <enabled>true</enabled>
                    <required>false</required>
                    <created_at>
                        <![CDATA[@string@.isDateTime()]]>
                    </created_at>
                    <updated_at>
                        <![CDATA[@string@.isDateTime()]]>
                    </updated_at>
                </locale>
            """
        And the locales should exist:
            | code | enabled | required |
            | fr   | yes     | no       |
