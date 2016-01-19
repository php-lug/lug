<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source statusCode.
 */

namespace Lug\Component\Behat\Extension\Api\Context;

use Behat\Behat\Context\Context;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ApiContextInterface extends Context
{
    /**
     * @param HttpClient $client
     */
    public function setClient(HttpClient $client);

    /**
     * @param RequestFactory $requestFactory
     */
    public function setRequestFactory(RequestFactory $requestFactory);

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl);
}
