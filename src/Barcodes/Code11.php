<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Antwerpes\Barcodes\Helpers\RegexHelper;

class Code11 extends Barcode
{
    /** @var string */
    protected const STOP_BITS = '1011001';

    /** @var string */
    protected const SEPARATOR = '0';

    /** @var array<int|string, string> */
    protected const BINARIES = [
        '0' => '101011',
        '1' => '1101011',
        '2' => '1001011',
        '3' => '1100101',
        '4' => '1011011',
        '5' => '1101101',
        '6' => '1001101',
        '7' => '1010011',
        '8' => '1101001',
        '9' => '110101',
        '-' => '101101',
    ];

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return RegexHelper::test($this->code, '/^[0-9\-]+$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $code = $this->code.$this->calculateChecksum($this->code, 10);

        if (mb_strlen($this->code) >= 10) {
            $code .= $this->calculateChecksum($code, 9);
        }

        $data = collect(mb_str_split($code))
            ->map(fn (string $char) => self::BINARIES[$char])
            ->join(self::SEPARATOR);

        return [
            $this->createEncoding([
                'data' => self::STOP_BITS.self::SEPARATOR.$data.self::SEPARATOR.self::STOP_BITS,
                'text' => $this->code,
            ]),
        ];
    }

    /**
     * Calculate the checksum.
     */
    protected function calculateChecksum(string $code, int $maxWeight): int
    {
        $characters = array_keys(self::BINARIES);
        $weights = range(1, $maxWeight);

        $sum = collect(mb_str_split(strrev($code)))->reduce(
            fn (int $carry, string $char, int $idx) => $carry + (array_search(
                $char,
                $characters,
                false,
            ) * $weights[$idx % count($weights)]),
            0,
        );

        return $sum % 11;
    }
}
