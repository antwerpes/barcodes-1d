<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\Barcodes\Barcode;

abstract class EAN extends Barcode
{
    /**
     * Encode the given code $data using the given $structure.
     */
    protected function encodeData(string $data, string $structure, string $separator = ''): string
    {
        return collect(mb_str_split($data))
            ->map(fn (string $value, int $idx) => Encodings::BINARIES[$structure[$idx]][(int) $value])
            ->join($separator);
    }
}
