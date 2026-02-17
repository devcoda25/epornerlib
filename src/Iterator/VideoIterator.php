<?php

declare(strict_types=1);

namespace EpornerLib\Iterator;

use EpornerLib\Client\EpornerClient;
use EpornerLib\Models\Video;
use EpornerLib\Models\VideoCollection;
use EpornerLib\Parameters\SearchParams;
use Iterator;

/**
 * Iterator for paginated video search results
 * 
 * Allows iterating through all videos matching search criteria
 * without manually handling pagination.
 */
class VideoIterator implements Iterator
{
    private EpornerClient $client;
    private SearchParams $params;
    private ?VideoCollection $currentCollection = null;
    private int $currentIndex = 0;
    private int $totalFetched = 0;
    private bool $hasMorePages = true;
    private ?Video $currentVideo = null;

    public function __construct(EpornerClient $client, SearchParams $params)
    {
        $this->client = $client;
        $this->params = $params;
    }

    /**
     * Rewind to the first video
     */
    public function rewind(): void
    {
        $this->currentIndex = 0;
        $this->totalFetched = 0;
        $this->hasMorePages = true;
        $this->fetchNextPage();
    }

    /**
     * Get the current video
     */
    public function current(): ?Video
    {
        return $this->currentVideo;
    }

    /**
     * Get the current index
     */
    public function key(): int
    {
        return $this->totalFetched;
    }

    /**
     * Move to the next video
     */
    public function next(): void
    {
        $this->currentIndex++;
        $this->totalFetched++;

        $this->loadCurrentVideo();
    }

    /**
     * Check if current position is valid
     */
    public function valid(): bool
    {
        return $this->currentVideo !== null;
    }

    /**
     * Load the current video from the collection
     */
    private function loadCurrentVideo(): void
    {
        // If we have a collection and the index is within bounds
        if (
            $this->currentCollection !== null
            && $this->currentIndex < $this->currentCollection->count()
        ) {
            $this->currentVideo = $this->currentCollection->videos[$this->currentIndex];
            return;
        }

        // Try to fetch more pages if available
        if ($this->hasMorePages) {
            $this->fetchNextPage();

            if (
                $this->currentCollection !== null
                && $this->currentIndex < $this->currentCollection->count()
            ) {
                $this->currentVideo = $this->currentCollection->videos[$this->currentIndex];
                return;
            }
        }

        // No more videos
        $this->currentVideo = null;
    }

    /**
     * Fetch the next page of results
     */
    private function fetchNextPage(): void
    {
        try {
            $this->currentCollection = $this->client->search($this->params);
            $this->currentIndex = 0;
            $this->hasMorePages = $this->currentCollection->hasMorePages();

            // Prepare next page params
            if ($this->hasMorePages) {
                $this->params = $this->params->nextPage();
            }
        } catch (\Throwable $e) {
            // On error, stop iteration
            $this->currentCollection = VideoCollection::empty();
            $this->hasMorePages = false;
            $this->currentVideo = null;
        }
    }

    /**
     * Get the total number of videos fetched so far
     */
    public function count(): int
    {
        return $this->totalFetched;
    }

    /**
     * Check if there are more pages to fetch
     */
    public function hasMore(): bool
    {
        return $this->hasMorePages;
    }

    /**
     * Get the current page number
     */
    public function getCurrentPage(): int
    {
        return $this->params->page;
    }
}
