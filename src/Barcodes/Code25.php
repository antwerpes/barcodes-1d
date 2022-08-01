<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Antwerpes\Barcodes\Helpers\RegexHelper;

class Code25 extends Barcode
{
    /** @var string */
    protected const START_BITS = '1110111010';

    /** @var string */
    protected const END_BITS = '111010111';

    /** @var string[] */
    protected const WIDTHS = [
        '00110', '10001', '01001', '11000', '00101',
        '10100', '01100', '00011', '10010', '01010',
    ];

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return RegexHelper::test($this->code, '/^[0-9]+$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $data = collect(mb_str_split($this->code))
            ->map(fn (string $digit) => $this->encodeDigit($digit))
            ->join('');

        return [
            $this->createEncoding(['data' => self::START_BITS.$data.self::END_BITS, 'text' => $this->code]),
        ];
    }

    /**
     * Encode a single digit according to the width encoding map.
     * 1 -> thick bar (111)
     * 0 -> thin bar (1)
     * + separator (0).
     */
    protected function encodeDigit(string $digit): string
    {
        return collect(mb_str_split(self::WIDTHS[$digit]))
            ->map(fn (string $width, int $idx) => ($width === '1' ? '111' : '1').'0')
            ->join('');
    }
}
