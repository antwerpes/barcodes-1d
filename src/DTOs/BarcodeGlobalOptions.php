<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\DTOs;

class BarcodeGlobalOptions
{
    public function __construct(
        public int $width,
        public int $text_margin,
        public string $color,
        public int|string $image_font,
        public string $image_format,
        public int $image_scale,
        public int $font_size,
        public bool $display_value,
        public int $margin_top,
        public int $margin_right,
        public int $margin_bottom,
        public int $margin_left,
        public ?string $background = null,
        public ?string $text_color = null,
    ) {}
}
