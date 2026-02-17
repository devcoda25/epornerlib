<?php

declare(strict_types=1);

namespace EpornerLib\Exceptions;

use EpornerLib\Constants\API;

/**
 * Exception thrown when parameter validation fails
 */
class ValidationException extends EpornerException
{
    private string $parameter;
    private mixed $value;

    public function __construct(
        string $parameter,
        mixed $value,
        string $message = '',
        ?\Throwable $previous = null
    ) {
        $this->parameter = $parameter;
        $this->value = $value;

        $message = $message ?: "Invalid value '{$value}' for parameter '{$parameter}'";

        parent::__construct($message, 0, $previous);
    }

    /**
     * Get the parameter name that failed validation
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * Get the invalid value
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Create an exception for invalid thumbsize
     */
    public static function invalidThumbSize(string $thumbsize): self
    {
        return new self(
            'thumbsize',
            $thumbsize,
            sprintf(
                "Invalid thumbsize '%s'. Valid values are: %s",
                $thumbsize,
                implode(', ', API::VALID_THUMB_SIZES)
            )
        );
    }

    /**
     * Create an exception for invalid order
     */
    public static function invalidOrder(string $order): self
    {
        return new self(
            'order',
            $order,
            sprintf(
                "Invalid order '%s'. Valid values are: %s",
                $order,
                implode(', ', API::VALID_ORDERS)
            )
        );
    }

    /**
     * Create an exception for invalid format
     */
    public static function invalidFormat(string $format): self
    {
        return new self(
            'format',
            $format,
            sprintf(
                "Invalid format '%s'. Valid values are: %s",
                $format,
                implode(', ', API::VALID_FORMATS)
            )
        );
    }

    /**
     * Create an exception for invalid per_page
     */
    public static function invalidPerPage(int $perPage): self
    {
        return new self(
            'per_page',
            $perPage,
            sprintf(
                "Invalid per_page '%d'. Valid range is: 1-%d",
                $perPage,
                API::MAX_PER_PAGE
            )
        );
    }

    /**
     * Create an exception for invalid page
     */
    public static function invalidPage(int $page): self
    {
        return new self(
            'page',
            $page,
            sprintf(
                "Invalid page '%d'. Valid range is: 1-%d",
                $page,
                API::MAX_PAGE
            )
        );
    }

    /**
     * Create an exception for invalid gay parameter
     */
    public static function invalidGay(int $gay): self
    {
        return new self(
            'gay',
            $gay,
            sprintf(
                "Invalid gay '%d'. Valid values are: %s",
                $gay,
                implode(', ', API::VALID_GAY_OPTIONS)
            )
        );
    }

    /**
     * Create an exception for invalid lq parameter
     */
    public static function invalidLq(int $lq): self
    {
        return new self(
            'lq',
            $lq,
            sprintf(
                "Invalid lq '%d'. Valid values are: %s",
                $lq,
                implode(', ', API::VALID_LQ_OPTIONS)
            )
        );
    }

    /**
     * Create an exception for missing required parameter
     */
    public static function missingRequired(string $parameter): self
    {
        return new self(
            $parameter,
            null,
            "Required parameter '{$parameter}' is missing"
        );
    }
}
