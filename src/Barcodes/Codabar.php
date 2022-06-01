<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Illuminate\Support\Str;

class Codabar extends Barcode
{
    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return Str::of($this->code)->test('/^[ABCD][0123456789\-\$\:\/\.\+]*[ABCD]$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $code = mb_strtoupper($this->code);
        $encodings = $this->getEncodings();

        $data = collect(mb_str_split($code))
            ->map(fn (string $value) => $encodings[$value])
            ->join('0');

        return [
            $this->createEncoding(['data' => $data, 'text' => Str::replace(['A', 'B', 'C', 'D'], '', $code)]),
        ];
    }

    protected function getEncodings(): array
    {
        return [
            '0' => '101010011',
            '1' => '101011001',
            '2' => '101001011',
            '3' => '110010101',
            '4' => '101101001',
            '5' => '110101001',
            '6' => '100101011',
            '7' => '100101101',
            '8' => '100110101',
            '9' => '110100101',
            '-' => '101001101',
            '$' => '101100101',
            ':' => '1101011011',
            '/' => '1101101011',
            '.' => '1101101101',
            '+' => '1011011011',
            'A' => '1011001001',
            'B' => '1010010011',
            'C' => '1001001011',
            'D' => '1010011001',
        ];
    }
}
