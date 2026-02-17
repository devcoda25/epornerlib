<?php

declare(strict_types=1);

namespace EpornerLib\Models;

use EpornerLib\Exceptions\EpornerException;

/**
 * Represents a removed video ID from the Eporner API
 */
final class RemovedVideo
{
    public readonly string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Create a RemovedVideo from an array
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw new EpornerException("Missing required field: id");
        }

        return new self((string) $data['id']);
    }

    /**
     * Create a RemovedVideo from just an ID string
     */
    public static function fromString(string $id): self
    {
        return new self($id);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return ['id' => $this->id];
    }

    /**
     * Convert to string (just returns the ID)
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
