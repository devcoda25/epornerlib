<?php

declare(strict_types=1);

use EpornerLib\Client\EpornerClient;
use EpornerLib\Models\Video;
use EpornerLib\Parameters\SearchParams;

/**
 * Create a new EpornerClient instance
 * 
 * @param string|null $apiKey Optional API key
 * @return EpornerClient
 */
function eporner(?string $apiKey = null): EpornerClient
{
    return new EpornerClient($apiKey);
}

/**
 * Search for videos with basic parameters
 * 
 * @param string $query Search query
 * @param int $perPage Results per page
 * @param string $order Sort order
 * @param string $thumbsize Thumbnail size
 * @return \EpornerLib\Models\VideoCollection
 */
function eporner_search(
    string $query,
    int $perPage = 30,
    string $order = 'latest',
    string $thumbsize = 'medium'
): \EpornerLib\Models\VideoCollection {
    $client = new EpornerClient();

    $params = new SearchParams(
        query: $query,
        perPage: $perPage,
        order: $order,
        thumbsize: $thumbsize
    );

    return $client->search($params);
}

/**
 * Get a video by ID
 * 
 * @param string $id Video ID
 * @param string $thumbsize Thumbnail size
 * @return Video|null
 */
function eporner_video(string $id, string $thumbsize = 'medium'): ?Video
{
    $client = new EpornerClient();

    return $client->getVideo($id);
}

/**
 * Format video duration from seconds to mm:ss or hh:mm:ss
 * 
 * @param int $seconds Duration in seconds
 * @return string Formatted duration
 */
function eporner_format_duration(int $seconds): string
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }

    return sprintf('%d:%02d', $minutes, $secs);
}

/**
 * Format view count with human-readable suffix
 * 
 * @param int $views Number of views
 * @return string Formatted view count
 */
function eporner_format_views(int $views): string
{
    if ($views >= 1000000) {
        return sprintf('%.1fM', $views / 1000000);
    }

    if ($views >= 1000) {
        return sprintf('%.1fK', $views / 1000);
    }

    return (string) $views;
}

/**
 * Format rating
 * 
 * @param float $rate Rating value
 * @return string Formatted rating
 */
function eporner_format_rating(float $rate): string
{
    return sprintf('%.2f', $rate);
}
