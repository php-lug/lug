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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\MinkAwareContext;
use Lug\Component\Behat\Dictionary\MinkDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class AjaxContext implements MinkAwareContext
{
    use MinkDictionary;

    public function waitAjax()
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getSession()->wait('10000', '!$.active');
        }
    }
}
