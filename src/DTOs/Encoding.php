<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class Encoding extends DataTransferObject
{
    public string $data;
    public ?string $text;
    public int $height;
    public string $align;
    public ?int $totalHeight;
    public ?int $totalWidth;
}
