<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\AdminBundle\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Lug\Component\Behat\Context\RoutingContext;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DashboardContext implements Context
{
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

        $this->routingContext = $environment->getContext(RoutingContext::class);
    }

    /**
     * @Given I am on the admin page
     */
    public function visitAdmin()
    {
        $this->routingContext->visit('lug_admin_main');
    }

    /**
     * @Given I am on the dashboard page
     */
    public function visitDashboard()
    {
        $this->routingContext->visit('lug_admin_dashboard');
    }

    /**
     * @Then I should be on the dashboard page
     */
    public function assertAddress()
    {
        $this->routingContext->assertAddress('lug_admin_dashboard');
    }
}
