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

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Lug\Bundle\ResourceBundle\Behat\Dictionary\ResourceDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractResourceContext implements KernelAwareContext
{
    use ResourceDictionary;

    /**
     * @param string $resource
     * @param mixed  $criteria
     *
     * @return object|null
     */
    public function assertResourceFound($resource, array $criteria)
    {
        \PHPUnit_Framework_Assert::assertNotNull(
            $result = $this->findResource($resource, $criteria),
            sprintf('The resource "%s" could not be found. (%s)', $resource, json_encode($criteria))
        );

        return $result;
    }

    /**
     * @param string $resource
     * @param mixed  $criteria
     */
    public function assertResourceNotFound($resource, array $criteria)
    {
        \PHPUnit_Framework_Assert::assertNull(
            $this->findResource($resource, $criteria),
            sprintf('The resource "%s" could be found. (%s)', $resource, json_encode($criteria))
        );
    }

    /**
     * @param string  $resource
     * @param mixed[] $criteria
     *
     * @return object|null
     */
    private function findResource($resource, array &$criteria)
    {
        array_walk_recursive($criteria, function (&$value) {
            if ($value === 'yes') {
                $value = true;
            } elseif ($value === 'no') {
                $value = false;
            }
        });

        $this->getManager($resource)->clear();

        return $this->getRepository($resource)->findOneBy($criteria);
    }
}
