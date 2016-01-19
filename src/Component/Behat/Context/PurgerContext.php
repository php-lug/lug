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

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Lug\Component\Behat\Dictionary\PurgerDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PurgerContext implements KernelAwareContext
{
    use PurgerDictionary;

    /**
     * @BeforeScenario
     */
    public function prepareDatabases()
    {
        $this->purgeDatabases();
    }
}
