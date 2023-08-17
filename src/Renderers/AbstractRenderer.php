<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Renderers;

use Antwerpes\Barcodes\DTOs\BarcodeGlobalOptions;
use Antwerpes\Barcodes\DTOs\Encoding;
use Illuminate\Support\Collection;

abstract class AbstractRenderer
{
    /**
     * @param Encoding[] $encodings
     */
    public function __construct(
        protected array $encodings,
        protected BarcodeGlobalOptions $options,
    ) {}

    abstract public function render(): string;

    /**
     * Get the total width of the final barcode representation.
     */
    protected function getTotalWidth(): int
    {
        $totalWidth = collect($this->encodings)->sum('totalWidth');

        return $totalWidth + $this->options->margin_left + $this->options->margin_right;
    }

    /**
     * Get the maximum height of all encodings.
     */
    protected function getMaxHeight(): int
    {
        return collect($this->encodings)->max('totalHeight');
    }

    /**
     * Get the starting x position of the text for the given $encoding.
     */
    protected function getTextStart(Encoding $encoding): int|float
    {
        return match ($encoding->align) {
            'left' => 0,
            'right' => $encoding->totalWidth - 1,
            'center' => $encoding->totalWidth / 2,
        };
    }

    /**
     * Split the encoding into chunks of adjacent `1` bits, disregarding all `0` bits and only
     * keeping the original array indexes of the `1` bits, which is what we need for correct
     * x-offset calculation.
     * e.g. '110010111' -> [[0, 1], [4], [6, 7, 8]].
     * We only care about the `1` bits (`0`s are empty spaces). We also want to group `1`s together,
     * so that we only need to draw one (wider) rectangle if two `1`s are next to each other.
     *
     * @return Collection<int, Collection<int, int>>
     *
     * @noinspection PhpDocSignatureIsNotCompleteInspection
     */
    protected function getEncodingChunks(string $data): Collection
    {
        return collect(mb_str_split($data))
            ->chunkWhile(fn ($value, $key, $chunk) => $value === $chunk->last())
            ->filter(fn (Collection $value) => $value->first() === '1')
            ->map(fn (Collection $value) => $value->keys());
    }
}
