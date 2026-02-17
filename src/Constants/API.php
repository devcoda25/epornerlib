<?php

declare(strict_types=1);

namespace EpornerLib\Constants;

/**
 * Eporner API v2 Constants
 * 
 * Contains all API endpoints, default values, and valid parameter options.
 */
final class API
{
    /**
     * Base URL for the Eporner API
     */
    public const BASE_URL = 'https://www.eporner.com';

    /**
     * API v2 endpoints
     */
    public const ENDPOINT_SEARCH = '/api/v2/video/search/';
    public const ENDPOINT_ID = '/api/v2/video/id/';
    public const ENDPOINT_REMOVED = '/api/v2/video/removed/';

    /**
     * Default parameter values
     */
    public const DEFAULT_QUERY = 'all';
    public const DEFAULT_PER_PAGE = 30;
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_THUMBSIZE = 'medium';
    public const DEFAULT_ORDER = 'latest';
    public const DEFAULT_GAY = 0;
    public const DEFAULT_LQ = 1;
    public const DEFAULT_FORMAT = 'json';

    /**
     * Maximum values
     */
    public const MAX_PER_PAGE = 1000;
    public const MAX_PAGE = 1000000;

    /**
     * Valid parameter values
     */
    public const VALID_THUMB_SIZES = ['small', 'medium', 'big'];
    public const VALID_ORDERS = [
        'latest',
        'longest',
        'shortest',
        'top-rated',
        'most-popular',
        'top-weekly',
        'top-monthly',
    ];
    public const VALID_FORMATS = ['json', 'xml'];
    public const VALID_GAY_OPTIONS = [0, 1, 2];
    public const VALID_LQ_OPTIONS = [0, 1, 2];

    /**
     * Thumb size dimensions
     */
    public const THUMB_DIMENSIONS = [
        'small' => ['width' => 190, 'height' => 152],
        'medium' => ['width' => 427, 'height' => 240],
        'big' => ['width' => 640, 'height' => 360],
    ];

    /**
     * Get the full API URL for a given endpoint
     */
    public static function getUrl(string $endpoint): string
    {
        return self::BASE_URL . $endpoint;
    }

    /**
     * Validate thumbsize parameter
     */
    public static function isValidThumbSize(string $thumbsize): bool
    {
        return in_array($thumbsize, self::VALID_THUMB_SIZES, true);
    }

    /**
     * Validate order parameter
     */
    public static function isValidOrder(string $order): bool
    {
        return in_array($order, self::VALID_ORDERS, true);
    }

    /**
     * Validate format parameter
     */
    public static function isValidFormat(string $format): bool
    {
        return in_array($format, self::VALID_FORMATS, true);
    }

    /**
     * Validate per_page parameter
     */
    public static function isValidPerPage(int $perPage): bool
    {
        return $perPage >= 1 && $perPage <= self::MAX_PER_PAGE;
    }

    /**
     * Validate page parameter
     */
    public static function isValidPage(int $page): bool
    {
        return $page >= 1 && $page <= self::MAX_PAGE;
    }
}
