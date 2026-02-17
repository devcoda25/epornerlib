<?php

declare(strict_types=1);

namespace EpornerLib\Parameters;

use EpornerLib\Constants\API;
use EpornerLib\Exceptions\ValidationException;

/**
 * Parameters for the video ID API method
 */
class VideoIdParams
{
    public string $thumbsize;
    public string $format;

    public function __construct(
        string $thumbsize = API::DEFAULT_THUMBSIZE,
        string $format = API::DEFAULT_FORMAT
    ) {
        $this->thumbsize = $thumbsize;
        $this->format = $format;
    }

    /**
     * Convert to array for API request
     *
     * @throws ValidationException If parameters are invalid
     */
    public function toArray(): array
    {
        $this->validate();

        return [
            'thumbsize' => $this->thumbsize,
            'format' => $this->format,
        ];
    }

    /**
     * Validate all parameters
     *
     * @throws ValidationException If any parameter is invalid
     */
    public function validate(): void
    {
        if (!API::isValidThumbSize($this->thumbsize)) {
            throw ValidationException::invalidThumbSize($this->thumbsize);
        }

        if (!API::isValidFormat($this->format)) {
            throw ValidationException::invalidFormat($this->format);
        }
    }

    /**
     * Set the thumbnail size
     */
    public function setThumbSize(string $thumbsize): self
    {
        $this->thumbsize = $thumbsize;
        return $this;
    }

    /**
     * Set response format
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Create from an array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            thumbsize: $data['thumbsize'] ?? API::DEFAULT_THUMBSIZE,
            format: $data['format'] ?? API::DEFAULT_FORMAT
        );
    }
}
