<?php

declare(strict_types=1);

namespace EpornerLib\Http;

use EpornerLib\Constants\API;
use EpornerLib\Exceptions\APIException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Client wrapper for making API requests
 */
class HttpClient
{
    private ClientInterface $client;
    private ?string $apiKey;

    public function __construct(?string $apiKey = null, ?ClientInterface $client = null)
    {
        $this->apiKey = $apiKey;
        $this->client = $client ?? new Client([
            'base_uri' => API::BASE_URL,
            'timeout' => 30.0,
            'connect_timeout' => 10.0,
            'http_errors' => true,
        ]);
    }

    /**
     * Make a GET request to the API
     *
     * @throws APIException If the request fails
     */
    public function get(string $endpoint, array $params = []): ResponseInterface
    {
        $uri = $this->buildUri($endpoint, $params);

        $headers = $this->getHeaders();

        $request = new Request('GET', $uri, $headers);

        try {
            return $this->client->send($request);
        } catch (GuzzleException $e) {
            throw APIException::fromNetworkError($e);
        }
    }

    /**
     * Build the URI with query parameters
     */
    private function buildUri(string $endpoint, array $params): string
    {
        if (empty($params)) {
            return $endpoint;
        }

        $query = http_build_query($params);
        return "{$endpoint}?{$query}";
    }

    /**
     * Get the headers for API requests
     */
    private function getHeaders(): array
    {
        $headers = [
            'User-Agent' => 'EpornerLib/1.0 (PHP)',
            'Accept' => 'application/json',
        ];

        if ($this->apiKey !== null) {
            $headers['Authorization'] = "Bearer {$this->apiKey}";
        }

        return $headers;
    }

    /**
     * Get the underlying Guzzle client
     */
    public function getGuzzleClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Set a custom Guzzle client
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Get the API key
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Set the API key
     */
    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}
