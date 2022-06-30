<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes;
use Antwerpes\Barcodes\Enumerators\Format;
use Antwerpes\Barcodes\Enumerators\Output;
use PHPUnit\Framework\TestCase;

class RenderingTest extends TestCase
{
    public function test_svg_encoding(): void
    {
        $svg = Barcodes::create('12345678', Format::CODE_128);
        $this->assertSame($svg, file_get_contents(__DIR__.'/fixtures/code128.svg'));
    }

    public function test_guard_bars(): void
    {
        $svg = Barcodes::create('1234567890128', Format::EAN_13);
        $this->assertSame($svg, file_get_contents(__DIR__.'/fixtures/ean13.svg'));
    }

    public function test_quiet_zones(): void
    {
        $svg = Barcodes::create('96385074', Format::EAN_8, Output::SVG, ['with_quiet_zone' => true]);
        $this->assertSame($svg, file_get_contents(__DIR__.'/fixtures/ean8.svg'));
    }

    public function test_image_encoding(): void
    {
        // PNG
        $image = Barcodes::create('96385074', Format::EAN_8, Output::PNG, ['with_quiet_zone' => true]);
        file_put_contents('img.png', base64_decode($image, true));
        $this->assertNotFalse(exif_imagetype('img.png'));

        // JPG
        $image = Barcodes::create('96385074', Format::EAN_8, Output::JPG, ['with_quiet_zone' => true]);
        file_put_contents('img.jpg', base64_decode($image, true));
        $this->assertNotFalse(exif_imagetype('img.jpg'));

        // WebP
        $image = Barcodes::create('96385074', Format::EAN_8, Output::WEBP, ['with_quiet_zone' => true]);
        file_put_contents('img.webp', base64_decode($image, true));
        $this->assertNotFalse(exif_imagetype('img.webp'));

        // Scaled up
        $image = Barcodes::create('96385074', Format::EAN_8, Output::PNG, [
            'with_quiet_zone' => true,
            'image_scale' => 2,
        ]);
        file_put_contents('img.png', base64_decode($image, true));
        $this->assertNotFalse(exif_imagetype('img.png'));

        unlink('img.png');
        unlink('img.jpg');
        unlink('img.webp');
    }
}
