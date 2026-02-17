<?php

declare(strict_types=1);

namespace EpornerLib\Exceptions;

/**
 * Exception thrown when the API returns an error
 */
class APIException extends EpornerException
{
    private ?int $statusCode;
    private ?string $responseBody;

    public function __construct(
        string $message,
        int $statusCode = null,
        string $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    /**
     * Get the HTTP status code
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Get the response body
     */
    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    /**
     * Create an exception from a Guzzle response
     */
    public static function fromGuzzleResponse(
        \GuzzleHttp\Psr7\Response $response,
        ?\Throwable $previous = null
    ): self {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        $message = "API returned status code: {$statusCode}";

        return new self($message, $statusCode, $body, $previous);
    }

    /**
     * Create an exception from a network error
     */
    public static function fromNetworkError(
        \GuzzleHttp\Exception\RequestException $exception
    ): self {
        $response = $exception->getResponse();

        if ($response !== null) {
            return self::fromGuzzleResponse($response, $exception);
        }

        $message = $exception->getMessage();

        return new self(
            "Network error: {$message}",
            null,
            null,
            $exception
        );
    }
}
