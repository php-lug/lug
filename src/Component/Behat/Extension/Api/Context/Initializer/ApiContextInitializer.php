<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source statusCode.
 */

namespace Lug\Component\Behat\Extension\Api\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Lug\Component\Behat\Extension\Api\Context\ApiContextInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ApiContextInitializer implements ContextInitializer
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @param string   $baseUrl
     * @param string[] $filePaths
     */
    public function __construct($baseUrl, array $filePaths)
    {
        $this->baseUrl = $baseUrl;
        $this->client = HttpClientDiscovery::find();
        $this->messageFactory = MessageFactoryDiscovery::find();
        $this->fileLocator = new FileLocator($filePaths);
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof ApiContextInterface) {
            return;
        }

        $context->setBaseUrl($this->baseUrl);
        $context->setClient($this->client);
        $context->setRequestFactory($this->messageFactory);
        $context->setFileLocator($this->fileLocator);
    }
}
