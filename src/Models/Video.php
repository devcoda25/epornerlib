<?php

declare(strict_types=1);

namespace EpornerLib\Models;

use EpornerLib\Exceptions\EpornerException;

/**
 * Represents a video from the Eporner API
 */
final class Video
{
    public readonly string $id;
    public readonly string $title;
    public readonly string $keywords;
    public readonly int $views;
    public readonly float $rate;
    public readonly string $url;
    public readonly string $added;
    public readonly int $lengthSec;
    public readonly string $lengthMin;
    public readonly string $embed;
    public readonly Thumb $defaultThumb;
    /** @var Thumb[] */
    public readonly array $thumbs;

    private function __construct(
        string $id,
        string $title,
        string $keywords,
        int $views,
        float $rate,
        string $url,
        string $added,
        int $lengthSec,
        string $lengthMin,
        string $embed,
        Thumb $defaultThumb,
        array $thumbs
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->keywords = $keywords;
        $this->views = $views;
        $this->rate = $rate;
        $this->url = $url;
        $this->added = $added;
        $this->lengthSec = $lengthSec;
        $this->lengthMin = $lengthMin;
        $this->embed = $embed;
        $this->defaultThumb = $defaultThumb;
        $this->thumbs = $thumbs;
    }

    /**
     * Create a Video from an array
     *
     * @throws EpornerException If required fields are missing
     */
    public static function fromArray(array $data): self
    {
        self::validateRequired($data);

        // Parse default_thumb
        $defaultThumb = isset($data['default_thumb'])
            ? Thumb::fromArray($data['default_thumb'])
            : new Thumb('medium', 0, 0, '');

        // Parse thumbs array
        $thumbs = [];
        if (isset($data['thumbs']) && is_array($data['thumbs'])) {
            foreach ($data['thumbs'] as $thumbData) {
                $thumbs[] = Thumb::fromArray($thumbData);
            }
        }

        return new self(
            id: (string) $data['id'],
            title: (string) $data['title'],
            keywords: (string) ($data['keywords'] ?? ''),
            views: (int) ($data['views'] ?? 0),
            rate: (float) ($data['rate'] ?? 0.0),
            url: (string) $data['url'],
            added: (string) ($data['added'] ?? ''),
            lengthSec: (int) ($data['length_sec'] ?? 0),
            lengthMin: (string) ($data['length_min'] ?? '0:00'),
            embed: (string) $data['embed'],
            defaultThumb: $defaultThumb,
            thumbs: $thumbs
        );
    }

    /**
     * Validate required fields
     *
     * @throws EpornerException If required fields are missing
     */
    private static function validateRequired(array $data): void
    {
        $required = ['id', 'title', 'url', 'embed'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new EpornerException("Missing required field: {$field}");
            }
        }
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'keywords' => $this->keywords,
            'views' => $this->views,
            'rate' => $this->rate,
            'url' => $this->url,
            'added' => $this->added,
            'length_sec' => $this->lengthSec,
            'length_min' => $this->lengthMin,
            'embed' => $this->embed,
            'default_thumb' => $this->defaultThumb->toArray(),
            'thumbs' => array_map(fn(Thumb $thumb) => $thumb->toArray(), $this->thumbs),
        ];
    }

    /**
     * Get video duration in a human-readable format
     */
    public function getDuration(): string
    {
        return $this->lengthMin;
    }

    /**
     * Get the video ID from the URL
     */
    public function getUrlId(): string
    {
        return $this->id;
    }

    /**
     * Get the embed HTML for this video
     */
    public function getEmbedHtml(int $width = 640, int $height = 360): string
    {
        return sprintf(
            '<iframe src="%s" width="%d" height="%d" frameborder="0" allowfullscreen></iframe>',
            $this->embed,
            $width,
            $height
        );
    }

    /**
     * Check if the video has any thumbnails
     */
    public function hasThumbs(): bool
    {
        return !empty($this->thumbs);
    }

    /**
     * Get the first thumbnail (usually the best quality)
     */
    public function getFirstThumb(): ?Thumb
    {
        return $this->thumbs[0] ?? $this->defaultThumb;
    }

    /**
     * Get keywords as an array
     *
     * @return string[]
     */
    public function getKeywordsArray(): array
    {
        if (empty($this->keywords)) {
            return [];
        }

        return array_map(
            'trim',
            explode(',', $this->keywords)
        );
    }
}
