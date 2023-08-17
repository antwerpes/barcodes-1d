<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\DTOs;

class Encoding
{
    public function __construct(
        public string $data,
        public int $height,
        public string $align,
        public ?string $text = null,
        public ?int $totalHeight = null,
        public ?int $totalWidth = null,
    ) {}

    public static function create(array $options): static
    {
        return new static(
            data: $options['data'],
            height: $options['height'],
            align: $options['align'],
            text: $options['text'] ?? null,
            totalHeight: $options['totalHeight'] ?? null,
            totalWidth: $options['totalWidth'] ?? null,
        );
    }
}
