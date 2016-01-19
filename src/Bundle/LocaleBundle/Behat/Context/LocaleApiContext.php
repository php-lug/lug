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
use Lug\Bundle\ResourceBundle\Behat\Context\ResourceApiContext;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleApiContext extends AbstractLocaleContext
{
    /**
     * @BeforeScenario
     */
    public function init(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->resourceContext = $environment->getContext(ResourceApiContext::class);
    }
}
