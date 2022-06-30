<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class BarcodeGlobalOptions extends DataTransferObject
{
    public int $width;
    public int $text_margin;
    public ?string $background;
    public string $color;
    public ?string $text_color;
    public string|int $image_font;
    public string $image_format;
    public int $image_scale;
    public int $font_size;
    public bool $display_value;
    public int $margin_top;
    public int $margin_right;
    public int $margin_bottom;
    public int $margin_left;
}
