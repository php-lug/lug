# This file is part of the Lug package.
#
# (c) Eric GELOEN <geloen.eric@gmail.com>
#
# For the full copyright and license information, please read the LICENSE
# file that was distributed with this source code.

@lug.api @lug.api.xml @lug.api.locale
Feature: Updating a locale
    In order to reach visitors from multiple countries
    As a developer
    I should be able to update a locale

    Background:
        Given the application has its default locale configured
        And there are the locales:
            | code | enabled | required |
            | fr   | yes     | yes      |
            | be   | no      | no       |
        And I set the header "Content-Type" with value "application/xml"
        And I set the header "Accept" with value "application/xml"

    Scenario: Missing locale fields
        Given I send a "PUT" request to "/locale/fr"
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

    Scenario Outline: Invalid locale fields
        Given I send a "<method>" request to "/locale/fr" with body:
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

        Examples:
            | method |
            | PUT    |
            | PATCH  |

    Scenario Outline: Duplicating locale code
        Given I send a "<method>" request to "/locale/fr" with body:
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

        Examples:
            | method |
            | PUT    |
            | PATCH  |

    Scenario Outline: Updating a locale
        Given I send a "<method>" request to "/locale/fr" with body:
            """
                <locale>
                    <code>es</code>
                    <enabled>false</enabled>
                    <required>false</required>
                </locale>
            """
        Then the response status code should be "204"
        And the response should be empty
        And the locales should exist:
            | code | enabled | required |
            | es   | no      | no       |
        And the locale "fr" should not exist

        Examples:
            | method |
            | PUT    |
            | PATCH  |

    Scenario: Patching a locale
        Given I send a "PATCH" request to "/locale/fr" with body:
            """
                <locale>
                    <code>es</code>
                </locale>
            """
        Then the response status code should be "204"
        And the response should be empty
        And the locales should exist:
            | code | enabled | required |
            | es   | yes     | yes      |
        And the locale "fr" should not exist
