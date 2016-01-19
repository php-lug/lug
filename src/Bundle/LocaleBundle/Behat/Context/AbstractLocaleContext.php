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

use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Lug\Bundle\LocaleBundle\Behat\Dictionary\LocaleDictionary;
use Lug\Bundle\ResourceBundle\Behat\Context\AbstractResourceContext;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLocaleContext implements KernelAwareContext
{
    use LocaleDictionary;

    /**
     * @var AbstractResourceContext
     */
    protected $resourceContext;

    /**
     * @param TableNode $table
     *
     * @Then the locales should exist:
     */
    public function assertResourcesFound(TableNode $table)
    {
        foreach ($table as $row) {
            $this->assertLocaleResourceFound($row);
        }
    }

    /**
     * @param TableNode $table
     *
     * @Then the locales should not exist:
     */
    public function assertResourcesNotFound(TableNode $table)
    {
        foreach ($table as $row) {
            $this->assertLocaleResourceNotFound($row);
        }
    }

    /**
     * @param string $code
     *
     * @Then the locale ":code" should exist
     */
    public function assertResourceFound($code)
    {
        $this->assertLocaleResourceFound(['code' => $code]);
    }

    /**
     * @param string $code
     *
     * @Then the locale ":code" should not exist
     */
    public function assertResourceNotFound($code)
    {
        $this->assertLocaleResourceNotFound(['code' => $code]);
    }

    /**
     * @param mixed[] $criteria
     */
    private function assertLocaleResourceFound(array $criteria)
    {
        $this->resourceContext->assertResourceFound('locale', $criteria);
    }

    /**
     * @param mixed[] $criteria
     */
    private function assertLocaleResourceNotFound(array $criteria)
    {
        $this->resourceContext->assertResourceNotFound('locale', $criteria);
    }
}
