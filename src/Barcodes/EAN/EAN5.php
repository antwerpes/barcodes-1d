<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\DTOs\Encoding;
use Illuminate\Support\Str;

/**
 * @see https://en.wikipedia.org/wiki/EAN-5
 */
class EAN5 extends EAN
{
    public const STRUCTURE = [
        'GGLLL', 'GLGLL', 'GLLGL', 'GLLLG', 'LGGLL',
        'LLGGL', 'LLLGG', 'LGLGL', 'LGLLG', 'LLGLG',
    ];
    public const START_BITS = '01011';
    public const SEPARATOR = '01';

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return Str::of($this->code)->test('/^[0-9]{5}$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $structure = self::STRUCTURE[$this->calculateChecksum()];
        $data = self::START_BITS.$this->encodeData($this->code, $structure, self::SEPARATOR);

        return [new Encoding(data: $data, text: $this->code)];
    }

    /**
     * Calculate the checksum for getting the correct structure. See https://en.wikipedia.org/wiki/EAN-5
     * for algorithm details.
     */
    protected function calculateChecksum(): int
    {
        $result = collect(mb_str_split($this->code))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 ? 3 : 9)),
            0,
        );

        return $result % 10;
    }
}
