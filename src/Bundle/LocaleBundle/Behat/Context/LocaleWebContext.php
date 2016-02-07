<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Lug\Bundle\GridBundle\Behat\Context\GridWebContext;
use Lug\Bundle\ResourceBundle\Behat\Context\ResourceWebContext;
use Lug\Component\Behat\Context\RoutingContext;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleWebContext extends AbstractLocaleContext
{
    /**
     * @var GridWebContext
     */
    private $gridContext;

    /**
     * @var RoutingContext
     */
    private $routingContext;

    /**
     * @BeforeScenario
     */
    public function init(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->gridContext = $environment->getContext(GridWebContext::class);
        $this->resourceContext = $environment->getContext(ResourceWebContext::class);
        $this->routingContext = $environment->getContext(RoutingContext::class);
    }

    /**
     * @Given I am on the locale listing page
     */
    public function visitIndex()
    {
        $this->visit('index');
    }

    /**
     * @Given I am on the locale creation page
     */
    public function visitCreate()
    {
        $this->visit('create');
    }

    /**
     * @param string $code
     *
     * @Given I am on the locale ":code" page
     */
    public function visitShow($code)
    {
        $this->visit('show', $code);
    }

    /**
     * @param string $code
     *
     * @Given I am on the locale ":code" edition page
     */
    public function visitUpdate($code)
    {
        $this->visit('update', $code);
    }

    /**
     * @Given I toggle the locale grid batches
     */
    public function toggleGridBatch()
    {
        $this->gridContext->toggleBatch();
    }

    /**
     * @param string $code
     *
     * @Given I select the locale grid batch ":code"
     */
    public function selectGridBatch($code)
    {
        $this->gridContext->selectBatch($code);
    }

    /**
     * @Given I select the "all" locale grid batch
     */
    public function selectGridBatchAll()
    {
        $this->gridContext->selectBatchAll();
    }

    /**
     * @param string $code
     * @param string $sorting
     *
     * @Given I follow the locale grid sorting link ":code" ":sorting"
     */
    public function followGridSortingLink($code, $sorting)
    {
        $this->gridContext->followSortingLink($code, $sorting);
    }

    /**
     * @param string $code
     *
     * @Given I follow the locale grid show link ":code"
     */
    public function followGridShowLink($code)
    {
        $this->gridContext->followColumnActionLink($code, 'Show');
    }

    /**
     * @param string $code
     *
     * @Given I follow the locale grid edition link ":code"
     */
    public function followGridEditionLink($code)
    {
        $this->gridContext->followColumnActionLink($code, 'Update');
    }

    /**
     * @param string $code
     *
     * @Given I follow the locale grid deletion link ":code"
     */
    public function followGridDeletionLink($code)
    {
        $this->gridContext->followColumnActionLink($code, 'Delete');
    }

    /**
     * @Given I wait the locale grid filters
     */
    public function waitGridFilters()
    {
        $this->gridContext->waitFilters();
    }

    /**
     * @Given I wait the locale grid filters refresh
     */
    public function waitGridFiltersRefresh()
    {
        $this->gridContext->waitFiltersRefresh();
    }

    /**
     * @param TableNode $table
     *
     * @Then the locale grid should be:
     */
    public function assertGrid(TableNode $table)
    {
        $this->gridContext->assertGrid($table->getRows());
    }

    /**
     * @param string $column
     * @param string $order
     *
     * @Then the locale grid column ":column" should be sorted ":order"
     */
    public function assertGridSorting($column, $order)
    {
        $this->gridContext->assertSorting($column, $order);
    }

    /**
     * @param string $column
     * @param string $cells
     *
     * @Given the locale grid for the column ":column" should be ":cells"
     */
    public function assertGridCells($column, $cells)
    {
        $this->gridContext->assertColumnCells(
            $column,
            array_map('trim', !empty($cells) ? explode(';', $cells) : [])
        );
    }

    /**
     * @Then all locale grid batches should be checked
     */
    public function assertGridBatchesChecked()
    {
        $this->gridContext->assertBatchesState(true);
    }

    /**
     * @Then all locale grid batches should not be checked
     */
    public function assertGridBatchesUnchecked()
    {
        $this->gridContext->assertBatchesState(false);
    }

    /**
     * @Then I should be on the locale listing page
     */
    public function assertIndexAddress()
    {
        $this->assertAddress('index');
    }

    /**
     * @Then I should be on the locale batching page
     */
    public function assertBatchAddress()
    {
        $this->assertAddress('batch');
    }

    /**
     * @Then I should be on the locale creation page
     */
    public function assertCreateAddress()
    {
        $this->assertAddress('create');
    }

    /**
     * @param string $code
     *
     * @Then I should be on the locale ":code" page
     */
    public function assertShowAddress($code)
    {
        $this->assertAddress('show', $code);
    }

    /**
     * @param string $code
     *
     * @Then I should be on the locale ":code" edition page
     */
    public function assertUpdateAddress($code)
    {
        $this->assertAddress('update', $code);
    }

    /**
     * @param string      $action
     * @param string|null $code
     */
    private function visit($action, $code = null)
    {
        $parameters = [];

        if ($code !== null) {
            $parameters['code'] = $code;
        }

        $this->resourceContext->visit('locale', $action, $parameters);
    }

    /**
     * @param string      $action
     * @param string|null $code
     */
    private function assertAddress($action, $code = null)
    {
        $parameters = [];

        if ($code !== null) {
            $parameters['code'] = $code;
        }

        $this->resourceContext->assertAddress('locale', $action, $parameters);
    }
}
