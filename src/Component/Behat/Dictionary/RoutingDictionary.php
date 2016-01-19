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

use Symfony\Component\Routing\RouterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait RoutingDictionary
{
    use ContainerDictionary;

    /**
     * @param string  $name
     * @param mixed[] $parameters
     * @param int     $referenceType
     *
     * @return string
     */
    public function generateUrl($name, array $parameters = [], $referenceType = RouterInterface::ABSOLUTE_PATH)
    {
        return $this->getRouter()->generate($name, $parameters, $referenceType);
    }

    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->getService('router');
    }
}
