<?php

declare(strict_types=1);

namespace EpornerLib\Models;

/**
 * Represents a video thumbnail
 */
final class Thumb
{
    public function __construct(
        public readonly string $size,
        public readonly int $width,
        public readonly int $height,
        public readonly string $src
    ) {
    }

    /**
     * Create a Thumb from an array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            size: $data['size'] ?? 'medium',
            width: (int) ($data['width'] ?? 0),
            height: (int) ($data['height'] ?? 0),
            src: $data['src'] ?? ''
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'src' => $this->src,
        ];
    }

    /**
     * Get the thumbnail dimensions as a string (e.g., "640x360")
     */
    public function getDimensions(): string
    {
        return "{$this->width}x{$this->height}";
    }

    /**
     * Check if this is a small thumbnail
     */
    public function isSmall(): bool
    {
        return $this->size === 'small';
    }

    /**
     * Check if this is a medium thumbnail
     */
    public function isMedium(): bool
    {
        return $this->size === 'medium';
    }

    /**
     * Check if this is a big thumbnail
     */
    public function isBig(): bool
    {
        return $this->size === 'big';
    }
}
