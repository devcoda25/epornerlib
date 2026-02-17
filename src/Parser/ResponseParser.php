<?php

declare(strict_types=1);

namespace EpornerLib\Parser;

use EpornerLib\Exceptions\EpornerException;
use EpornerLib\Models\RemovedVideo;
use EpornerLib\Models\Video;
use EpornerLib\Models\VideoCollection;
use Psr\Http\Message\ResponseInterface;

/**
 * Parser for API responses (JSON and XML)
 */
class ResponseParser
{
    /**
     * Parse a search response
     */
    public function parseSearch(ResponseInterface $response): VideoCollection
    {
        $data = $this->parseBody($response);

        return VideoCollection::fromArray($data);
    }

    /**
     * Parse a video by ID response
     */
    public function parseVideo(ResponseInterface $response): ?Video
    {
        $data = $this->parseBody($response);

        // Empty response means video was removed
        if (empty($data)) {
            return null;
        }

        return Video::fromArray($data);
    }

    /**
     * Parse a removed videos response
     *
     * @return RemovedVideo[]
     */
    public function parseRemoved(ResponseInterface $response): array
    {
        $data = $this->parseBody($response);

        if (!is_array($data)) {
            return [];
        }

        $removed = [];

        foreach ($data as $item) {
            $removed[] = RemovedVideo::fromArray($item);
        }

        return $removed;
    }

    /**
     * Parse the response body based on content type
     *
     * @throws EpornerException If parsing fails
     */
    private function parseBody(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if (empty($body)) {
            return [];
        }

        $contentType = $response->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            return $this->parseJson($body);
        }

        if (str_contains($contentType, 'xml')) {
            return $this->parseXml($body);
        }

        // Try JSON first, then XML
        try {
            return $this->parseJson($body);
        } catch (\Throwable $e) {
            return $this->parseXml($body);
        }
    }

    /**
     * Parse JSON body
     *
     * @throws EpornerException If JSON parsing fails
     */
    private function parseJson(string $body): array
    {
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EpornerException(
                "Failed to parse JSON response: " . json_last_error_msg()
            );
        }

        return $data ?? [];
    }

    /**
     * Parse XML body
     *
     * @throws EpornerException If XML parsing fails
     */
    private function parseXml(string $body): array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_string($body);

            if ($xml === false) {
                $errors = libxml_get_errors();
                $errorMessage = !empty($errors)
                    ? $errors[0]->message
                    : 'Unknown XML parsing error';

                throw new EpornerException("Failed to parse XML response: {$errorMessage}");
            }

            return $this->xmlToArray($xml);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    /**
     * Convert SimpleXMLElement to array
     */
    private function xmlToArray(\SimpleXMLElement $xml): array
    {
        $result = [];

        foreach ($xml as $key => $value) {
            // Handle attributes
            $attributes = [];
            foreach ($value->attributes() as $attrKey => $attrValue) {
                $attributes['@' . $attrKey] = (string) $attrValue;
            }

            // Handle child elements
            if ($value->count() > 0) {
                $children = $this->xmlToArray($value);

                if (!empty($attributes)) {
                    $children = array_merge($attributes, $children);
                }

                $result[$key] = $children;
            } else {
                // Leaf node
                $valueStr = (string) $value;

                if (!empty($attributes)) {
                    $result[$key] = array_merge(['#' => $valueStr], $attributes);
                } else {
                    $result[$key] = $valueStr;
                }
            }
        }

        return $result;
    }

    /**
     * Parse removed videos from TXT format
     *
     * @return RemovedVideo[]
     */
    public function parseRemovedTxt(string $body): array
    {
        $lines = explode("\n", trim($body));

        $removed = [];

        foreach ($lines as $line) {
            $id = trim($line);

            if (!empty($id)) {
                $removed[] = RemovedVideo::fromString($id);
            }
        }

        return $removed;
    }
}
