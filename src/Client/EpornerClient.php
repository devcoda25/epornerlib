<?php

declare(strict_types=1);

namespace EpornerLib\Client;

use EpornerLib\Constants\API;
use EpornerLib\Exceptions\ValidationException;
use EpornerLib\Http\HttpClient;
use EpornerLib\Iterator\VideoIterator;
use EpornerLib\Models\RemovedVideo;
use EpornerLib\Models\Video;
use EpornerLib\Models\VideoCollection;
use EpornerLib\Parameters\SearchParams;
use EpornerLib\Parameters\VideoIdParams;
use EpornerLib\Parser\ResponseParser;
use GuzzleHttp\ClientInterface;

/**
 * Main Eporner API Client
 * 
 * Provides access to all Eporner API v2 methods:
 * - Search for videos
 * - Get video by ID
 * - Get removed videos
 */
class EpornerClient
{
    private HttpClient $httpClient;
    private ResponseParser $parser;

    public function __construct(
        ?string $apiKey = null,
        ?ClientInterface $httpClient = null
    ) {
        $this->httpClient = new HttpClient($apiKey, $httpClient);
        $this->parser = new ResponseParser();
    }

    /**
     * Search for videos
     *
     * @throws ValidationException If parameters are invalid
     */
    public function search(SearchParams $params): VideoCollection
    {
        $params->validate();

        $response = $this->httpClient->get(
            API::ENDPOINT_SEARCH,
            $params->toArray()
        );

        return $this->parser->parseSearch($response);
    }

    /**
     * Get a single video by ID
     *
     * @param string $id The video ID
     * @param VideoIdParams|null $params Optional parameters
     * @return Video|null Returns null if video was removed
     */
    public function getVideo(string $id, ?VideoIdParams $params = null): ?Video
    {
        if (empty($id)) {
            throw new ValidationException('id', $id, 'Video ID cannot be empty');
        }

        $params = $params ?? new VideoIdParams();
        $params->validate();

        $requestParams = array_merge(
            ['id' => $id],
            $params->toArray()
        );

        $response = $this->httpClient->get(
            API::ENDPOINT_ID,
            $requestParams
        );

        return $this->parser->parseVideo($response);
    }

    /**
     * Get removed video IDs
     *
     * @param string $format Response format (json, xml, txt)
     * @return RemovedVideo[]
     */
    public function getRemovedVideos(string $format = 'json'): array
    {
        if (!in_array($format, ['json', 'xml', 'txt'], true)) {
            throw ValidationException::invalidFormat($format);
        }

        $response = $this->httpClient->get(
            API::ENDPOINT_REMOVED,
            ['format' => $format]
        );

        // Handle TXT format specially
        if ($format === 'txt') {
            return $this->parser->parseRemovedTxt((string) $response->getBody());
        }

        return $this->parser->parseRemoved($response);
    }

    /**
     * Get an iterator for paginated video search results
     *
     * @throws ValidationException If parameters are invalid
     */
    public function searchIterator(SearchParams $params): VideoIterator
    {
        return new VideoIterator($this, $params);
    }

    /**
     * Get the HTTP client instance
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Set a new API key
     */
    public function setApiKey(string $apiKey): void
    {
        $this->httpClient->setApiKey($apiKey);
    }

    /**
     * Get the current API key
     */
    public function getApiKey(): ?string
    {
        return $this->httpClient->getApiKey();
    }
}
