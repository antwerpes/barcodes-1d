<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

trait CalculatesUPCAChecksum
{
    /**
     * Calculate the checksum for getting the correct structure. See
     * https://en.wikipedia.org/wiki/International_Article_Number for algorithm details.
     */
    protected function calculateUPCAChecksum(string $code): int
    {
        $result = collect(mb_str_split(mb_substr($code, 0, 7)))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 === 0 ? 3 : 1)),
            0,
        );

        return (10 - ($result % 10)) % 10;
    }
}
