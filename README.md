# EpornerLib

Official PHP client library for the Eporner API v2.

[![PHP](https://img.shields.io/packagist/php-v/eporner/epornerlib)](https://packagist.org/packages/eporner/epornerlib)
[![Latest Version](https://img.shields.io/packagist/v/eporner/epornerlib)](https://packagist.org/packages/eporner/epornerlib)
[![License](https://img.shields.io/packagist/l/eporner/epornerlib)](LICENSE)

EpornerLib is a modern PHP 8+ library that provides an easy-to-use interface for the Eporner API v2. It features strict typing, comprehensive error handling, and a clean object-oriented design.

## Features

- Full Eporner API v2 support (search, video by ID, removed videos)
- Strict typing with PHP 8.0+
- Comprehensive parameter validation
- Support for JSON and XML responses
- Paginated results iterator
- Custom Guzzle HTTP client support
- PSR-4 autoloading
- Helper functions for quick access

## Requirements

- PHP 8.0 or higher
- Guzzle HTTP client 7.0+
- JSON extension

## Installation

Install via Composer:

```bash
composer require eporner/epornerlib
```

## Quick Start

```php
<?php

use EpornerLib\Client\EpornerClient;
use EpornerLib\Parameters\SearchParams;

$client = new EpornerClient();

// Search for videos
$params = new SearchParams(
    query: 'teen',
    perPage: 50,
    order: 'most-popular',
    thumbsize: 'big'
);

$results = $client->search($params);

echo "Total videos found: " . $results->totalCount . "\n";

foreach ($results->videos as $video) {
    echo "- " . $video->title . " (" . $video->views . " views)\n";
}
```

## Usage Examples

### Search Videos

```php
<?php

use EpornerLib\Client\EpornerClient;
use EpornerLib\Parameters\SearchParams;

$client = new EpornerClient();

$params = new SearchParams(
    query: 'anal',
    perPage: 100,
    order: 'latest',
    thumbsize: 'big',
    page: 1
);

$results = $client->search($params);

// Access pagination info
echo "Page: " . $results->page . "/" . $results->totalPages . "\n";
echo "Total: " . $results->totalCount . " videos\n";

// Iterate through videos
foreach ($results->videos as $video) {
    echo $video->title . "\n";
    echo $video->url . "\n";
    echo "Duration: " . $video->lengthMin . "\n";
    echo "Rating: " . $video->rate . "/5\n";
}
```

### Get Single Video

```php
<?php

$video = $client->getVideo('IsabYDAiqXa');

if ($video) {
    echo "Title: " . $video->title . "\n";
    echo "Views: " . $video->views . "\n";
    echo "Rating: " . $video->rate . "\n";
    echo "Duration: " . $video->lengthMin . "\n";
    echo "Embed URL: " . $video->embed . "\n";
    
    // Get default thumbnail
    echo "Thumbnail: " . $video->defaultThumb->src . "\n";
    
    // Get all thumbnails
    foreach ($video->thumbs as $thumb) {
        echo "Thumb: " . $thumb->src . "\n";
    }
}
```

### Using the Iterator (Automatic Pagination)

```php
<?php

use EpornerLib\Client\EpornerClient;
use EpornerLib\Parameters\SearchParams;

$client = new EpornerClient();

$params = new SearchParams(
    query: 'popular',
    perPage: 100,
    order: 'most-popular'
);

// Iterate through ALL matching videos automatically
$iterator = $client->searchIterator($params);

foreach ($iterator as $video) {
    echo $video->title . "\n";
    
    // Optional: limit the total videos
    if ($iterator->count() >= 1000) {
        break;
    }
}
```

### Get Removed Videos

```php
<?php

$removed = $client->getRemovedVideos();

foreach ($removed as $video) {
    echo "Removed ID: " . $video->id . "\n";
}

// For large lists, use TXT format (smaller output)
$removedTxt = $client->getRemovedVideos('txt');
```

### Using Helper Functions

```php
<?php

// Quick client creation
$client = eporner();

// Quick search
$results = eporner_search('teen', 50, 'most-popular');

// Get video
$video = eporner_video('IsabYDAiqXa');

// Format helpers
$duration = eporner_format_duration(2539); // "42:19"
$views = eporner_format_views(260221); // "260.2K"
$rating = eporner_format_rating(4.13); // "4.13"
```

## API Parameters

### Search Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| query | string | "all" | Search query |
| per_page | int | 30 | Results per page (1-1000) |
| page | int | 1 | Page number |
| thumbsize | string | "medium" | Thumbnail size (small, medium, big) |
| order | string | "latest" | Sort order |
| gay | int | 0 | Include gay content (0=exclude, 1=include, 2=only gay) |
| lq | int | 1 | Include low quality (0=exclude, 1=include, 2=only LQ) |
| format | string | "json" | Response format (json, xml) |

### Sort Orders

- `latest` - Newest first
- `longest` - Longest first
- `shortest` - Shortest first
- `top-rated` - Top rated first
- `most-popular` - Most popular all time
- `top-weekly` - Most popular this week
- `top-monthly` - Most popular this month

## Error Handling

```php
<?php

use EpornerLib\Client\EpornerClient;
use EpornerLib\Exceptions\ValidationException;
use EpornerLib\Exceptions\APIException;

$client = new EpornerClient();

try {
    $results = $client->search($params);
} catch (ValidationException $e) {
    echo "Invalid parameter: " . $e->getParameter() . "\n";
    echo "Value: " . $e->getValue() . "\n";
} catch (APIException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
    echo "Status: " . $e->getStatusCode() . "\n";
}
```

## Using Custom Guzzle Client

```php
<?php

use EpornerLib\Client\EpornerClient;
use GuzzleHttp\Client;

$guzzle = new Client([
    'base_uri' => 'https://www.eporner.com',
    'timeout' => 60.0,
]);

$client = new EpornerClient(null, $guzzle);
```

## License

MIT License - see [LICENSE](LICENSE) file.

## Disclaimer

This library is not affiliated with or endorsed by Eporner. It is a third-party implementation of their public API.
