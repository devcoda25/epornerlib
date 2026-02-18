<?php

declare(strict_types=1);

namespace EpornerLib\Http;

use EpornerLib\Constants\API;
use EpornerLib\Exceptions\APIException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Client wrapper for making API requests
 * Uses native PHP HTTP as primary method (more reliable on some systems)
 */
class HttpClient
{
    private ?ClientInterface $client;
    private ?string $apiKey;
    private bool $useNativeHttp;

    public function __construct(?string $apiKey = null, ?ClientInterface $client = null)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
        $this->useNativeHttp = ($client === null); // Use native if no custom client provided
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

        // Use native PHP HTTP if no custom Guzzle client was provided
        if ($this->useNativeHttp) {
            return $this->getWithNativeHttp($uri, $headers);
        }

        $request = new Request('GET', $uri, $headers);

        try {
            return $this->client->send($request);
        } catch (RequestException $e) {
            throw APIException::fromNetworkError($e);
        }
    }

    /**
     * Make GET request using native PHP (file_get_contents)
     * This is more reliable on systems where Guzzle has DNS issues
     */
    private function getWithNativeHttp(string $uri, array $headers): ResponseInterface
    {
        $baseUrl = API::BASE_URL;
        $fullUrl = $baseUrl . $uri;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $this->headersToString($headers),
                'timeout' => 30,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $responseBody = @file_get_contents($fullUrl, false, $context);

        if ($responseBody === false) {
            $error = error_get_last();
            $message = $error['message'] ?? 'Unknown error';
            
            // Check for specific error types and provide better messages
            if (empty($error)) {
                // Try to get more info from the $http_response_header
                if (!empty($http_response_header)) {
                    $responseHeaders = $this->parseResponseHeaders($http_response_header);
                    if (isset($responseHeaders['http_code']) && $responseHeaders['http_code'] >= 400) {
                        throw new \Exception("HTTP error: " . $responseHeaders['http_code']);
                    }
                }
                throw new \Exception("Native HTTP request failed: Empty response (possible network issue)");
            }
            
            // Check for common errors
            if (stripos($message, 'Could not resolve host') !== false || stripos($message, 'Name or service not known') !== false) {
                throw new \Exception("DNS resolution failed: Cannot resolve host 'www.eporner.com'");
            }
            if (stripos($message, 'Connection refused') !== false) {
                throw new \Exception("Connection refused: Cannot connect to eporner.com");
            }
            if (stripos($message, 'timed out') !== false || stripos($message, 'timeout') !== false) {
                throw new \Exception("Connection timed out: The request took too long");
            }
            
            throw new \Exception("Native HTTP request failed: {$message}");
        }

        // Parse response headers
        $responseHeaders = $this->parseResponseHeaders($http_response_header ?? []);
        $statusCode = $responseHeaders['http_code'] ?? 200;

        return new Response(
            $statusCode,
            $responseHeaders,
            $responseBody
        );
    }

    /**
     * Convert headers array to string
     */
    private function headersToString(array $headers): string
    {
        $str = '';
        foreach ($headers as $name => $value) {
            $str .= "{$name}: {$value}\r\n";
        }
        return $str;
    }

    /**
     * Parse response headers from native PHP
     */
    private function parseResponseHeaders(array $rawHeaders): array
    {
        $headers = [];
        $headers['http_code'] = 200;

        foreach ($rawHeaders as $header) {
            if (preg_match('/^HTTP\/\d+\.\d+\s+(\d+)/', $header, $matches)) {
                $headers['http_code'] = (int) $matches[1];
            } elseif (strpos($header, ':') !== false) {
                list($name, $value) = explode(':', $header, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }
        }

        return $headers;
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
