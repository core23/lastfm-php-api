<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\LastFm\Connection;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Nucleos\LastFm\Exception\ApiException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @deprecated use PsrClientConnection instead
 */
final class HTTPlugConnection implements ConnectionInterface
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * Initialize client.
     */
    public function __construct(HttpClient $client, MessageFactory $messageFactory, string $endpoint = ConnectionInterface::DEFAULT_ENDPOINT)
    {
        $this->client         = $client;
        $this->messageFactory = $messageFactory;
        $this->endpoint       = $endpoint;
    }

    public function getPageBody(string $url, array $params = [], string $method = 'GET'): ?string
    {
        $request  = $this->createRequest($method, $url, $params);

        try {
            $response = $this->client->sendRequest($request);
        } catch (Exception $e) {
            throw new ApiException(
                sprintf('Error fetching page body for url: %s', (string) $request->getUri()),
                $e->getCode(),
                $e
            );
        }

        if ($response->getStatusCode() >= 400) {
            return null;
        }

        return $response->getBody()->getContents();
    }

    public function call(string $url, array $params = [], string $method = 'GET'): array
    {
        $params  = array_merge($params, ['format' => 'json']);
        $request = $this->createRequest($method, $this->endpoint, $params);

        try {
            $response = $this->client->sendRequest($request);

            // Parse response
            return $this->parseResponse($response);
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApiException('Technical error occurred.', 500, $e);
        } catch (Exception $e) {
            throw new ApiException('Technical error occurred.', $e->getCode(), $e);
        }
    }

    private function createRequest(string $method, string $url, array $params): RequestInterface
    {
        $query = http_build_query($params);

        if ('POST' === $method) {
            return $this->messageFactory->createRequest($method, $url, [], $query);
        }

        return $this->messageFactory->createRequest($method, $url.'?'.$query);
    }

    /**
     * @throws ApiException
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $array = json_decode($response->getBody()->getContents(), true);

        if (\is_array($array) && \array_key_exists('error', $array) && \array_key_exists('message', $array)) {
            throw new ApiException($array['message'], $array['error']);
        }

        return $array;
    }
}
