<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Behat\Dictionary;

use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait PurgerDictionary
{
    use ContainerDictionary;

    /**
     * @var PurgerInterface[]
     */
    private $purgerCache = [];

    public function purgeDatabases()
    {
        $purgers = [];

        if ($this->hasService($doctrineOrm = 'doctrine')) {
            $purgers[ResourceInterface::DRIVER_DOCTRINE_ORM] = $this->getService($doctrineOrm)->getManagerNames();
        }

        if ($this->hasService($doctrineMongoDB = 'doctrine_mongodb')) {
            $purgers[ResourceInterface::DRIVER_DOCTRINE_MONGODB] = $this->getService($doctrineMongoDB)->getManagerNames();
        }

        foreach ($purgers as $driver => $managers) {
            foreach ($managers as $manager) {
                $this->getPurger($driver, $manager)->purge();
            }
        }
    }

    /**
     * @param string $driver
     * @param string $manager
     *
     * @return PurgerInterface
     */
    public function getPurger($driver, $manager)
    {
        if (isset($this->purgerCache[$hash = sha1($driver.'.'.$manager)])) {
            return $this->purgerCache[$hash];
        }

        $manager = $this->getService($manager);

        return $this->purgerCache[$hash] = $driver === ResourceInterface::DRIVER_DOCTRINE_MONGODB
            ? new MongoDBPurger($manager)
            : new ORMPurger($manager);
    }
}
