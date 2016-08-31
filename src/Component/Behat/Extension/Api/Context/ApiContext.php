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

use Behat\Gherkin\Node\PyStringNode;
use Coduo\PHPMatcher\Matcher;
use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Lug\Component\Behat\Extension\Api\Matcher\MatcherFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ApiContext implements ApiContextInterface
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @var MultipartStreamBuilder|null
     */
    private $multipartStreamBuilder;

    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string[][]
     */
    private $headers = [];

    /**
     * @var RequestInterface|null
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @var resource[]
     */
    private $resources = [];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->matcher = (new MatcherFactory())->createMatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function setClient(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestFactory(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setStreamFactory(StreamFactory $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileLocator(FileLocatorInterface $fileFileLocator)
    {
        $this->fileLocator = $fileFileLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($baseUrl)
    {
        if (strrpos($baseUrl, '/') === strlen($baseUrl) - 1) {
            $baseUrl = substr($baseUrl, 0, -1);
        }

        $this->baseUrl = $baseUrl;
    }

    /**
     * @BeforeScenario
     *
     * @Given I reset the API context
     */
    public function reset()
    {
        $this->headers = [];
        $this->request = null;
        $this->response = null;
        $this->multipartStreamBuilder = null;

        foreach ($this->resources as $resource) {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }

        $this->resources = [];
    }

    /**
     * @param string $header
     * @param string $value
     *
     * @Given I set the header ":header" with value ":value"
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = [$value];
    }

    /**
     * @param string $header
     * @param string $value
     *
     * @Given I add the header ":header" with value ":value"
     */
    public function addHeader($header, $value)
    {
        $this->headers[$header][] = $value;
    }

    /**
     * @param string $header
     *
     * @Given I remove the header ":header"
     */
    public function removeHeader($header)
    {
        unset($this->headers[$header]);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @Given I set the field ":name" with value ":value"
     */
    public function setField($name, $value)
    {
        $this->getMultipartStreamBuilder()->addResource($name, $value);
    }

    /**
     * @param string      $name
     * @param string      $file
     * @param string|null $filename
     *
     * @Given I set the field ":name" with file ":file"
     * @Given I set the field ":name" with file ":file" and filename ":filename"
     */
    public function setFile($name, $file, $filename = null)
    {
        $path = $this->fileLocator->locate($file);
        $this->resources[] = $resource = fopen($path, 'r');
        $this->getMultipartStreamBuilder()->addResource($name, $resource, ['filename' => $filename]);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @When I send a ":method" request to ":url"
     */
    public function send($method, $url)
    {
        $this->request = $this->requestFactory->createRequest($method, $this->prepareUrl($url), $this->headers);

        $this->sendRequest();
    }

    /**
     * @param string       $method
     * @param string       $url
     * @param PyStringNode $string
     *
     * @When I send a ":method" request to ":url" with body:
     */
    public function sendWith($method, $url, PyStringNode $string)
    {
        $this->request = $this->requestFactory->createRequest(
            $method,
            $this->prepareUrl($url),
            $this->headers,
            trim($string->getRaw())
        );

        $this->sendRequest();
    }

    /**
     * @param string $statusCode
     *
     * @Then the response status code should be ":statusCode"
     */
    public function assertResponseStatusCode($statusCode)
    {
        \PHPUnit_Framework_Assert::assertSame((int) $statusCode, $this->getResponse()->getStatusCode());
    }

    /**
     * @param PyStringNode $json
     *
     * @Then the response should contain:
     */
    public function assertResponseContains(PyStringNode $json)
    {
        $this->matcher->match((string) $this->response->getBody(), $json->getRaw());

        \PHPUnit_Framework_Assert::assertNull($this->matcher->getError());
    }

    /**
     * @Then the response should be empty
     */
    public function assertResponseEmpty()
    {
        \PHPUnit_Framework_Assert::assertEmpty((string) $this->getResponse()->getBody());
    }

    /**
     * @Then I print the response
     */
    public function printResponse()
    {
        echo sprintf(
            "%s %s => %d:\n%s",
            $this->getRequest()->getMethod(),
            (string) $this->getRequest()->getUri(),
            $this->getResponse()->getStatusCode(),
            (string) $this->getResponse()->getBody()
        );
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        if ($this->request === null) {
            throw new \RuntimeException('You should create a request before using it.');
        }

        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        if ($this->response === null) {
            throw new \RuntimeException('You should send a request before using the response.');
        }

        return $this->response;
    }

    /**
     * @return MultipartStreamBuilder
     */
    private function getMultipartStreamBuilder()
    {
        if ($this->multipartStreamBuilder === null) {
            $this->multipartStreamBuilder = new MultipartStreamBuilder($this->streamFactory);
        }

        return $this->multipartStreamBuilder;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function prepareUrl($url)
    {
        return $this->baseUrl.$url;
    }

    private function sendRequest()
    {
        $request = $this->getRequest();

        if ($this->multipartStreamBuilder !== null) {
            $request = $request
                ->withBody($this->multipartStreamBuilder->build())
                ->withHeader(
                    'Content-Type',
                    'multipart/form-data;boundary='.$this->multipartStreamBuilder->getBoundary()
                );
        }

        try {
            $this->response = $this->client->sendRequest($request);
        } catch (HttpException $e) {
            $this->response = $e->getResponse();

            if ($this->response === null) {
                throw $e;
            }
        }
    }
}
