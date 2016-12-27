<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Lug\Component\Behat\Extension\Api\Context\ApiContext;
use Symfony\Component\Serializer\Encoder\ChainDecoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridApiContext implements Context
{
    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @BeforeScenario
     */
    public function init(BeforeScenarioScope $scope)
    {
        $this->apiContext = $scope->getEnvironment()->getContext(ApiContext::class);

        $this->decoder = new ChainDecoder([
            new JsonDecode(true),
            new XmlEncoder('result'),
        ]);
    }

    /**
     * @param string $property
     * @param string $sort
     * @param string $format
     *
     * @Given the ":format" response should be sorted by ":property" ":sort"
     */
    public function assertSorting($property, $sort, $format)
    {
        $data = $sortedData = $this->decodeByProperty($property, $format);

        array_multisort($sortedData, $sort === 'ASC' ? SORT_ASC : SORT_DESC, SORT_STRING | SORT_FLAG_CASE);

        \PHPUnit_Framework_Assert::assertSame(
            $sortedData,
            $data,
            sprintf(
                'The sorting does not match for the property "%s". Expected "%s", got "%s".',
                $property,
                json_encode($sortedData),
                json_encode($data)
            )
        );
    }

    /**
     * @param string $property
     * @param string $values
     * @param string $format
     *
     * @Given the ":format" response should be filtered by ":property" ":values"
     */
    public function assertResponseFiltering($property, $values, $format)
    {
        \PHPUnit_Framework_Assert::assertSame(
            $expected = !empty($values) ? array_map('trim', explode(';', $values)) : [],
            $data = $this->decodeByProperty($property, $format),
            sprintf(
                'The filtering does not match for the property "%s". Expected "%s", got "%s".',
                $property,
                json_encode($expected),
                json_encode($data)
            )
        );
    }

    /**
     * @param string $property
     * @param string $format
     *
     * @return mixed[]
     */
    private function decodeByProperty($property, $format)
    {
        return array_map(function ($entry) use ($property) {
            \PHPUnit_Framework_Assert::assertInternalType('array', $entry);
            \PHPUnit_Framework_Assert::assertArrayHasKey($property, $entry);

            return $entry[$property];
        }, $this->decode($format));
    }

    /**
     * @param $format
     *
     * @return array|mixed
     */
    private function decode($format)
    {
        $data = $this->decoder->decode((string) $this->apiContext->getResponse()->getBody(), $format);

        if ($format === 'json' && isset($data['_embedded']['items'])) {
            $data = $data['_embedded']['items'];
        }

        if ($format === 'xml') {
            if (!empty($data) && isset($data['entry'])) {
                if (is_int(key($data['entry']))) {
                    $data = $data['entry'];
                } elseif (isset($data['entry']['@rel'])) {
                    $data = array_pop($data['entry']);
                } else {
                    $data = [$data['entry']];
                }
            } else {
                $data = [];
            }
        }

        return $data;
    }
}
