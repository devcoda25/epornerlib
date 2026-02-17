<?php

declare(strict_types=1);

namespace EpornerLib\Models;

/**
 * Represents a collection of videos returned from a search query
 */
final class VideoCollection
{
    /** @var Video[] */
    public readonly array $videos;
    public readonly int $count;
    public readonly int $start;
    public readonly int $perPage;
    public readonly int $page;
    public readonly int $timeMs;
    public readonly int $totalCount;
    public readonly int $totalPages;

    private function __construct(
        array $videos,
        int $count,
        int $start,
        int $perPage,
        int $page,
        int $timeMs,
        int $totalCount,
        int $totalPages
    ) {
        $this->videos = $videos;
        $this->count = $count;
        $this->start = $start;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->timeMs = $timeMs;
        $this->totalCount = $totalCount;
        $this->totalPages = $totalPages;
    }

    /**
     * Create a VideoCollection from an array
     */
    public static function fromArray(array $data): self
    {
        $videos = [];

        if (isset($data['videos']) && is_array($data['videos'])) {
            foreach ($data['videos'] as $videoData) {
                $videos[] = Video::fromArray($videoData);
            }
        }

        return new self(
            videos: $videos,
            count: (int) ($data['count'] ?? 0),
            start: (int) ($data['start'] ?? 0),
            perPage: (int) ($data['per_page'] ?? 30),
            page: (int) ($data['page'] ?? 1),
            timeMs: (int) ($data['time_ms'] ?? 0),
            totalCount: (int) ($data['total_count'] ?? 0),
            totalPages: (int) ($data['total_pages'] ?? 0)
        );
    }

    /**
     * Create an empty VideoCollection
     */
    public static function empty(): self
    {
        return new self(
            videos: [],
            count: 0,
            start: 0,
            perPage: 30,
            page: 1,
            timeMs: 0,
            totalCount: 0,
            totalPages: 0
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'videos' => array_map(fn(Video $video) => $video->toArray(), $this->videos),
            'count' => $this->count,
            'start' => $this->start,
            'per_page' => $this->perPage,
            'page' => $this->page,
            'time_ms' => $this->timeMs,
            'total_count' => $this->totalCount,
            'total_pages' => $this->totalPages,
        ];
    }

    /**
     * Check if there are videos in this collection
     */
    public function isEmpty(): bool
    {
        return empty($this->videos);
    }

    /**
     * Check if there are more pages available
     */
    public function hasMorePages(): bool
    {
        return $this->page < $this->totalPages;
    }

    /**
     * Get the next page number
     */
    public function getNextPage(): ?int
    {
        return $this->hasMorePages() ? $this->page + 1 : null;
    }

    /**
     * Get the previous page number
     */
    public function getPreviousPage(): ?int
    {
        return $this->page > 1 ? $this->page - 1 : null;
    }

    /**
     * Get the number of videos in this collection
     */
    public function count(): int
    {
        return count($this->videos);
    }

    /**
     * Get iterator (for foreach support)
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->videos);
    }
}
