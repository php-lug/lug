<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Behat\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkAwareContext;
use Lug\Component\Behat\Dictionary\MinkDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormContext implements MinkAwareContext
{
    use MinkDictionary;

    /**
     * @param string $field
     * @param string $value
     *
     * @Given I fill in embed ":field" with ":value"
     */
    public function fillEmbedField($field, $value)
    {
        $this->findEmbedField($field)->setValue($value);
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @Given I select ":value" from embed ":field"
     */
    public function selectEmbedFieldOption($field, $value)
    {
        $this->findEmbedField($field)->selectOption($value);
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @Given the ":field" embed field should contain ":value"
     */
    public function assertEmbedFieldContains($field, $value)
    {
        $this->assertEmbedField($field, $value);
    }

    /**
     * @param string $field
     * @param string $option
     *
     * @Then the option ":option" from the select ":field" should exist
     */
    public function assertSelectOptionFound($field, $option)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($option, $this->findSelectOptions($field), true),
            sprintf('The select option "%s" from the select "%s" does not exist.', $option, $field)
        );
    }

    /**
     * @param string $field
     * @param string $option
     *
     * @Then the option ":option" from the select ":field" should not exist
     */
    public function assertSelectOptionNotFound($field, $option)
    {
        \PHPUnit_Framework_Assert::assertFalse(
            in_array($option, $this->findSelectOptions($field), true),
            sprintf('The select option "%s" from the select "%s" exists.', $option, $field)
        );
    }

    /**
     * @param NodeElement|string $field
     * @param string             $value
     */
    public function assertEmbedField($field, $value)
    {
        if (!$field instanceof NodeElement) {
            $field = $this->findEmbedField($field);
        }

        $text = $field->getText();

        if (empty($value)) {
            \PHPUnit_Framework_Assert::assertEmpty(
                $text,
                sprintf('The grid column body should be empty, got "%s".', $field->getValue(), $text)
            );
        } else {
            \PHPUnit_Framework_Assert::assertContains(
                $value,
                $text,
                sprintf(
                    'The grid column body "%s" does not contain "%s", got "%s".',
                    $field->getValue(),
                    $value,
                    $text
                )
            );
        }
    }

    /**
     * @param string $field
     *
     * @return NodeElement
     */
    private function findEmbedField($field)
    {
        $page = $node = $this->getPage();
        $followingSiblingXPath = '/following-sibling::*';
        $labelXpathPattern = '//label[contains(text(), "%s")]';

        $labels = array_map('trim', explode('->', $field));

        foreach ($labels as $label) {
            if ($node !== $page) {
                $node = $node->find('xpath', $followingSiblingXPath);
            }

            \PHPUnit_Framework_Assert::assertNotNull(
                $node,
                $message = sprintf('The field label "%s" could not be found.', $label)
            );

            $node = $node->find('xpath', sprintf($labelXpathPattern, $label));

            \PHPUnit_Framework_Assert::assertNotNull($node, $message);
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $node->hasAttribute('for'),
            sprintf('The attribute "for" of the embed field "%s" could not be found.', $field)
        );

        $field = $page->findById($for = $node->getAttribute('for'));

        \PHPUnit_Framework_Assert::assertNotNull(
            $field,
            sprintf('The embed field "%s" could not be found.', $for)
        );

        return $field;
    }

    /**
     * @param $field
     *
     * @return string[]
     */
    private function findSelectOptions($field)
    {
        \PHPUnit_Framework_Assert::assertNotNull(
            $select = $this->getPage()->findField($field),
            sprintf('The select field "%s" does not exist.', $field)
        );

        $options = [];

        foreach ($select->find('xpath', '/option') as $option) {
            $options[] = $option->getValue();
        }

        return $options;
    }
}
