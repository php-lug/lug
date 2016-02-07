<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkAwareContext;
use Lug\Component\Behat\Context\AjaxContext;
use Lug\Component\Behat\Dictionary\MinkDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridWebContext implements MinkAwareContext
{
    use MinkDictionary;

    /**
     * @var AjaxContext
     */
    private $ajaxContext;

    /**
     * @BeforeScenario
     */
    public function init(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->ajaxContext = $environment->getContext(AjaxContext::class);
    }

    public function toggleBatch()
    {
        $batch = $this->findBatch();

        $batch->isChecked() ? $batch->check() : $batch->uncheck();
    }

    /**
     * @param string|null $cell
     */
    public function selectBatch($cell = null)
    {
        $this->findBatch($cell)->check();
    }

    public function selectBatchAll()
    {
        $xpath = '//input[@id="grid_batch_all"]';

        \PHPUnit_Framework_Assert::assertNotNull(
            $node = $this->findGrid()->find('xpath', $xpath),
            'The grid "all" batch could not be found.'
        );

        $node->check();
    }

    /**
     * @param string|int $header
     * @param string     $sorting
     */
    public function followSortingLink($header, $sorting)
    {
        $xpath = '//a[@title="'.($sorting === 'ASC' ? 'Ascending' : 'Descending').'"]';

        \PHPUnit_Framework_Assert::assertNotNull(
            $node = $this->findHeader($header)->find('xpath', $xpath),
            sprintf('The grid column sorting link "%s" (%s) could not be found.', $header, $sorting)
        );

        $node->click();
    }

    /**
     * @param string $cell
     * @param string $link
     */
    public function followColumnActionLink($cell, $link)
    {
        $xpath = '/../td[last()]//*[text()[contains(., "'.$link.'")]]';

        \PHPUnit_Framework_Assert::assertNotNull(
            $node = $this->findCell(null, null, $cell)->find('xpath', $xpath),
            sprintf('The column action link "%s" of the cell "%s" could not be found.', $link, $cell)
        );

        $node->click();
    }

    public function waitFilters()
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getSession()->wait('10000', '$(\'#grid_modal h4\').is(\':visible\')');
        }
    }

    public function waitFiltersRefresh()
    {
        $this->ajaxContext->waitAjax();
    }

    /**
     * @param mixed[] $rows
     */
    public function assertGrid(array $rows)
    {
        $this->assertHeaders($rows[0]);
        $this->assertBody(array_slice($rows, 1));
    }

    /**
     * @param string[] $columns
     */
    public function assertHeaders(array $columns)
    {
        $offset = $this->hastBatch() ? 2 : 1;

        foreach ($columns as $column => $value) {
            $this->assertHeader($column + $offset, $value);
        }
    }

    /**
     * @param string|int $header
     * @param string     $value
     */
    public function assertHeader($header, $value)
    {
        \PHPUnit_Framework_Assert::assertContains(
            $value,
            $text = $this->findHeader($header)->getText(),
            sprintf('The grid column header "%s" does not contain "%s", got "%s".', $header, $value, $text)
        );
    }

    /**
     * @param bool $checked
     */
    public function assertBatchesState($checked)
    {
        $xpath = '/tr/td[1]/input';

        foreach ($this->findGrid()->findAll('xpath', $xpath) as $batch) {
            \PHPUnit_Framework_Assert::assertSame(
                $checked,
                $batch->isChecked(),
                sprintf('The grid batch "%s" should %s checked.', $batch->getValue(), $checked ? 'be' : 'not be')
            );
        }
    }

    /**
     * @param string[][] $rows
     */
    public function assertBody(array $rows)
    {
        foreach ($rows as $row => $columns) {
            $this->assertRow($row + 1, $columns);
        }
    }

    /**
     * @param int      $row
     * @param string[] $columns
     */
    public function assertRow($row, array $columns)
    {
        $offset = $this->hastBatch() ? 2 : 1;

        foreach ($columns as $column => $value) {
            $this->assertCell($row, $column + $offset, $value);
        }
    }

    /**
     * @param string[] $header
     * @param string[] $cells
     */
    public function assertColumnCells($header, array $cells)
    {
        $column = $this->findHeaderIndex($header);
        $offset = $this->hastBatch() ? 1 : 0;

        foreach ($cells as $row => $value) {
            $this->assertCell($row + 1, $column + $offset, $value);
        }
    }

    /**
     * @param int    $row
     * @param int    $column
     * @param string $value
     */
    public function assertCell($row, $column, $value)
    {
        \PHPUnit_Framework_Assert::assertContains(
            $value,
            $text = $this->findCell($row, $column)->getText(),
            sprintf(
                'The grid column body "%s" does not contain "%s at position %d, %d".',
                $text,
                $value,
                $row,
                $column
            )
        );
    }

    /**
     * @param string $header
     * @param string $sort
     */
    public function assertSorting($header, $sort)
    {
        $index = $this->findHeaderIndex($header);

        $values = $sortedValues = array_map(function (NodeElement $node) {
            return $node->getText();
        }, $this->findCell(null, $index));

        array_multisort($sortedValues, $sort === 'ASC' ? SORT_ASC : SORT_DESC);

        \PHPUnit_Framework_Assert::assertSame(
            $sortedValues,
            $values,
            sprintf(
                'The grid sorting does not match for the column "%s". Expected "%s", got "%s".',
                $header,
                json_encode($sortedValues),
                json_encode($values)
            )
        );
    }

    /**
     * @return bool
     */
    public function hastBatch()
    {
        try {
            $this->findBatch();

            return true;
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            return false;
        }
    }

    /**
     * @param string|null $cell
     *
     * @return NodeElement
     */
    public function findBatch($cell = null)
    {
        if ($cell === null) {
            \PHPUnit_Framework_Assert::assertNotNull(
                $batch = $this->findHeader(1)->find('xpath', '/input'),
                'The grid toggle batch checkbox could not be found.'
            );

            return $batch;
        }

        $xpath = '/../td[1]/input';

        \PHPUnit_Framework_Assert::assertNotNull(
            $batch = $this->findCell(null, null, $cell)->find('xpath', $xpath),
            sprintf('The grid column batch checkbox of the cell "%s" could not be found.', $cell)
        );

        return $batch;
    }

    /**
     * @param string $header
     *
     * @return int
     */
    public function findHeaderIndex($header)
    {
        $xpath = $this->findHeader($header)->getXpath().'/preceding::*';

        return count($this->getPage()->findAll('xpath', $xpath)) + 1;
    }

    /**
     * @param string|int $header
     *
     * @return NodeElement
     */
    public function findHeader($header)
    {
        $xpath = '/thead/tr/th'.(is_int($header) ? '['.$header.']' : '[contains(text(), "'.$header.'")]');

        \PHPUnit_Framework_Assert::assertNotNull(
            $node = $this->findGrid()->find('xpath', $xpath),
            sprintf('The grid header "%s" could not be found.', $header)
        );

        return $node;
    }

    /**
     * @param int|null    $row
     * @param int|null    $column
     * @param string|null $value
     *
     * @return NodeElement|NodeElement[]
     */
    public function findCell($row = null, $column = null, $value = null)
    {
        $rowXPath = $row;
        $columnXPath = $column;
        $valueXPath = $value;

        if ($row !== null) {
            $rowXPath = '['.$row.']';
        }

        if ($column !== null) {
            $columnXPath = '['.$column.']';
        }

        if ($value !== null) {
            $valueXPath = '[contains(text(), "'.$value.'")]';
        }

        $xpath = '/tbody/tr'.$rowXPath.'/td'.$columnXPath.$valueXPath;

        if ($value !== null || ($row !== null && $column !== null)) {
            \PHPUnit_Framework_Assert::assertNotNull(
                $cell = $this->findGrid()->find('xpath', $xpath),
                sprintf(
                    'The grid cell could not be found (row: %s, column: %s, value: %s).',
                    $row !== null ? $row : 'none',
                    $column !== null ? $column : 'none',
                    $value !== null ? $value : 'none'
                )
            );

            return $cell;
        }

        return $this->findGrid()->findAll('xpath', $xpath);
    }

    /**
     * @return NodeElement
     */
    public function findGrid()
    {
        $xpath = '//section[contains(@class, "content")]//table';

        \PHPUnit_Framework_Assert::assertNotNull(
            $grid = $this->getPage()->find('xpath', $xpath),
            'The grid could not be found.'
        );

        return $grid;
    }
}
