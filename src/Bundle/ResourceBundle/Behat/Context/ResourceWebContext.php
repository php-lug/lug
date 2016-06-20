<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkAwareContext;
use Lug\Component\Behat\Context\RoutingContext;
use Lug\Component\Behat\Dictionary\MinkDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceWebContext extends AbstractResourceContext implements MinkAwareContext
{
    use MinkDictionary;

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
     * @param string  $resource
     * @param string  $action
     * @param mixed[] $parameters
     */
    public function visit($resource, $action, array $parameters = [])
    {
        $this->routingContext->visit($this->getResourceRoute($resource, $action), $parameters);
    }

    /**
     * @param string  $resource
     * @param string  $action
     * @param mixed[] $parameters
     */
    public function assertAddress($resource, $action, array $parameters = [])
    {
        $this->routingContext->assertAddress($this->getResourceRoute($resource, $action), $parameters);
    }

    /**
     * @param string $resource
     * @param string $action
     *
     * @return string
     */
    public function getResourceRoute($resource, $action)
    {
        return 'lug_admin_'.str_replace([' ', '.'], '_', $resource).'_'.$action;
    }
}
