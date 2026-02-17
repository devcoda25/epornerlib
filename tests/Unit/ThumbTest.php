<?php

declare(strict_types=1);

namespace EpornerLib\Tests\Unit;

use EpornerLib\Models\Thumb;
use PHPUnit\Framework\TestCase;

class ThumbTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'size' => 'big',
            'width' => 640,
            'height' => 360,
            'src' => 'https://example.com/thumb.jpg',
        ];

        $thumb = Thumb::fromArray($data);

        $this->assertEquals('big', $thumb->size);
        $this->assertEquals(640, $thumb->width);
        $this->assertEquals(360, $thumb->height);
        $this->assertEquals('https://example.com/thumb.jpg', $thumb->src);
    }

    public function testToArray(): void
    {
        $thumb = new Thumb('medium', 427, 240, 'https://example.com/thumb.jpg');

        $result = $thumb->toArray();

        $this->assertEquals('medium', $result['size']);
        $this->assertEquals(427, $result['width']);
        $this->assertEquals(240, $result['height']);
        $this->assertEquals('https://example.com/thumb.jpg', $result['src']);
    }

    public function testGetDimensions(): void
    {
        $thumb = new Thumb('big', 640, 360, 'https://example.com/thumb.jpg');

        $this->assertEquals('640x360', $thumb->getDimensions());
    }

    public function testIsSmall(): void
    {
        $thumb = new Thumb('small', 190, 152, 'https://example.com/thumb.jpg');

        $this->assertTrue($thumb->isSmall());
        $this->assertFalse($thumb->isMedium());
        $this->assertFalse($thumb->isBig());
    }

    public function testIsMedium(): void
    {
        $thumb = new Thumb('medium', 427, 240, 'https://example.com/thumb.jpg');

        $this->assertFalse($thumb->isSmall());
        $this->assertTrue($thumb->isMedium());
        $this->assertFalse($thumb->isBig());
    }

    public function testIsBig(): void
    {
        $thumb = new Thumb('big', 640, 360, 'https://example.com/thumb.jpg');

        $this->assertFalse($thumb->isSmall());
        $this->assertFalse($thumb->isMedium());
        $this->assertTrue($thumb->isBig());
    }
}
