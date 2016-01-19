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

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Lug\Component\Behat\Dictionary\MinkDictionary;
use Lug\Component\Behat\Dictionary\RoutingDictionary;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RoutingContext implements KernelAwareContext, MinkAwareContext
{
    use MinkDictionary;
    use RoutingDictionary;

    /**
     * @param string  $name
     * @param mixed[] $parameters
     */
    public function visit($name, array $parameters = [])
    {
        $this->visitPath($this->generateUrl($name, $parameters));
        $this->assertStatusCode(Response::HTTP_OK);
    }

    /**
     * @param string  $name
     * @param mixed[] $parameters
     */
    public function assertAddress($name, array $parameters = [])
    {
        $this->assertSession()->addressEquals($this->generateUrl($name, $parameters));
        $this->assertStatusCode(Response::HTTP_OK);
    }

    /**
     * @param int $statusCode
     */
    private function assertStatusCode($statusCode)
    {
        try {
            $this->assertSession()->statusCodeEquals($statusCode);
        } catch (UnsupportedDriverActionException $e) {
        }
    }
}
