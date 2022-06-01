<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Illuminate\Support\Str;

class ITF14 extends Barcode
{
    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return
            Str::of($this->code)->test('/^[0-9]{14}$/')
            && ((int) $this->code[13]) === $this->calculateChecksum($this->code);
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
    }

    /**
     * Calculate the checksum for getting the correct structure. See
     * https://en.wikipedia.org/wiki/International_Article_Number for algorithm details.
     */
    protected function calculateChecksum(string $code): int
    {
        $result = collect(mb_str_split(mb_substr($code, 0, 12)))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 !== 0 ? 3 : 1)),
            0,
        );

        return (10 - ($result % 10)) % 10;
    }
}
