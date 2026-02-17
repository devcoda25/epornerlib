<?php

declare(strict_types=1);

namespace EpornerLib\Parameters;

use EpornerLib\Constants\API;
use EpornerLib\Exceptions\ValidationException;

/**
 * Parameters for the search API method
 */
class SearchParams
{
    public string $query;
    public int $perPage;
    public int $page;
    public string $thumbsize;
    public string $order;
    public int $gay;
    public int $lq;
    public string $format;

    public function __construct(
        string $query = API::DEFAULT_QUERY,
        int $perPage = API::DEFAULT_PER_PAGE,
        int $page = API::DEFAULT_PAGE,
        string $thumbsize = API::DEFAULT_THUMBSIZE,
        string $order = API::DEFAULT_ORDER,
        int $gay = API::DEFAULT_GAY,
        int $lq = API::DEFAULT_LQ,
        string $format = API::DEFAULT_FORMAT
    ) {
        $this->query = $query;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->thumbsize = $thumbsize;
        $this->order = $order;
        $this->gay = $gay;
        $this->lq = $lq;
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
            'query' => $this->query,
            'per_page' => $this->perPage,
            'page' => $this->page,
            'thumbsize' => $this->thumbsize,
            'order' => $this->order,
            'gay' => $this->gay,
            'lq' => $this->lq,
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

        if (!API::isValidOrder($this->order)) {
            throw ValidationException::invalidOrder($this->order);
        }

        if (!API::isValidFormat($this->format)) {
            throw ValidationException::invalidFormat($this->format);
        }

        if (!API::isValidPerPage($this->perPage)) {
            throw ValidationException::invalidPerPage($this->perPage);
        }

        if (!API::isValidPage($this->page)) {
            throw ValidationException::invalidPage($this->page);
        }

        if (!in_array($this->gay, API::VALID_GAY_OPTIONS, true)) {
            throw ValidationException::invalidGay($this->gay);
        }

        if (!in_array($this->lq, API::VALID_LQ_OPTIONS, true)) {
            throw ValidationException::invalidLq($this->lq);
        }
    }

    /**
     * Set the query/search term
     */
    public function setQuery(string $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Set the number of results per page
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Set the page number
     */
    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
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
     * Set the sort order
     */
    public function setOrder(string $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Set gay content filter
     * 0 = exclude, 1 = include, 2 = only gay
     */
    public function setGay(int $gay): self
    {
        $this->gay = $gay;
        return $this;
    }

    /**
     * Set low quality filter
     * 0 = exclude, 1 = include, 2 = only LQ
     */
    public function setLq(int $lq): self
    {
        $this->lq = $lq;
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
     * Create a new instance with the next page
     */
    public function nextPage(): self
    {
        return new self(
            query: $this->query,
            perPage: $this->perPage,
            page: $this->page + 1,
            thumbsize: $this->thumbsize,
            order: $this->order,
            gay: $this->gay,
            lq: $this->lq,
            format: $this->format
        );
    }

    /**
     * Create a new instance with a specific page
     */
    public function withPage(int $page): self
    {
        return new self(
            query: $this->query,
            perPage: $this->perPage,
            page: $page,
            thumbsize: $this->thumbsize,
            order: $this->order,
            gay: $this->gay,
            lq: $this->lq,
            format: $this->format
        );
    }

    /**
     * Create from an array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            query: $data['query'] ?? API::DEFAULT_QUERY,
            perPage: (int) ($data['per_page'] ?? API::DEFAULT_PER_PAGE),
            page: (int) ($data['page'] ?? API::DEFAULT_PAGE),
            thumbsize: $data['thumbsize'] ?? API::DEFAULT_THUMBSIZE,
            order: $data['order'] ?? API::DEFAULT_ORDER,
            gay: (int) ($data['gay'] ?? API::DEFAULT_GAY),
            lq: (int) ($data['lq'] ?? API::DEFAULT_LQ),
            format: $data['format'] ?? API::DEFAULT_FORMAT
        );
    }
}
