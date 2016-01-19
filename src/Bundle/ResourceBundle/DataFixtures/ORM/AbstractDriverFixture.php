<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDriverFixture extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if ($this->getResource()->getDriver() === $this->getDriver()) {
            $this->doLoad($manager);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    abstract protected function doLoad(ObjectManager $manager);

    /**
     * @return string
     */
    abstract protected function getDriver();
}
