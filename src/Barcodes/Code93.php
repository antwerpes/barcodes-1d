<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Illuminate\Support\Arr;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Code93 extends Barcode
{
    /** @var string */
    protected const END_BAR = '1';

    /** @var array<int|string, string> */
    protected const BINARIES = [
        '0' => '100010100',
        '1' => '101001000',
        '2' => '101000100',
        '3' => '101000010',
        '4' => '100101000',
        '5' => '100100100',
        '6' => '100100010',
        '7' => '101010000',
        '8' => '100010010',
        '9' => '100001010',
        'A' => '110101000',
        'B' => '110100100',
        'C' => '110100010',
        'D' => '110010100',
        'E' => '110010010',
        'F' => '110001010',
        'G' => '101101000',
        'H' => '101100100',
        'I' => '101100010',
        'J' => '100110100',
        'K' => '100011010',
        'L' => '101011000',
        'M' => '101001100',
        'N' => '101000110',
        'O' => '100101100',
        'P' => '100010110',
        'Q' => '110110100',
        'R' => '110110010',
        'S' => '110101100',
        'T' => '110100110',
        'U' => '110010110',
        'V' => '110011010',
        'W' => '101101100',
        'X' => '101100110',
        'Y' => '100110110',
        'Z' => '100111010',
        '-' => '100101110',
        '.' => '111010100',
        ' ' => '111010010',
        '$' => '111001010',
        '/' => '101101110',
        '+' => '101110110',
        '%' => '110101110',
        '#' => '100100110', // Placeholder for ($)
        '&' => '111011010', // Placeholder for (%)
        '@' => '111010110', // Placeholder for (/)
        '~' => '100110010', // Placeholder for (+)
        '*' => '101011110',
    ];

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        $allowed = $this->options['full_ascii']
            ? self::BINARIES
            : Arr::except(self::BINARIES, ['#', '&', '@', '~']);
        $code = $this->options['full_ascii'] ? $this->replaceASCIICharacters($this->code) : $this->code;

        return collect(mb_str_split($code))
            ->every(fn (string $value) => array_key_exists($value, $allowed));
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $code = str_replace('*', '', $this->code);

        if ($this->options['full_ascii']) {
            $code = $this->replaceASCIICharacters($code);
        }

        $code .= $this->calculateChecksum($code, 20);
        $code .= $this->calculateChecksum($code, 15);
        $code = '*'.$code.'*';

        $data = collect(mb_str_split($code))
            ->map(fn (string $char) => self::BINARIES[$char])
            ->join('');

        return [
            $this->createEncoding(['data' => $data.self::END_BAR, 'text' => $this->code]),
        ];
    }

    /**
     * Replace ASCII characters with their 2-character representation.
     */
    protected function replaceASCIICharacters(string $code): string
    {
        $representations = array_map(fn (string $sequence) => str_replace(
            ['$', '%', '/', '+'],
            ['#', '&', '@', '~'],
            $sequence,
        ), Code39::REPRESENTATIONS);

        return collect(mb_str_split($code))
            ->map(fn (string $char) => $representations[ord($char)] ?? $char)
            ->join('');
    }

    /**
     * Calculate the checksum.
     */
    protected function calculateChecksum(string $code, int $maxWeight): string
    {
        $characters = array_keys(self::BINARIES);
        $weights = range(1, $maxWeight);

        $sum = collect(mb_str_split(strrev($code)))->reduce(
            fn (int $carry, string $char, int $idx) => $carry + (array_search(
                $char,
                $characters,
                true,
            ) * $weights[$idx % count($weights)]),
            0,
        );

        return (string) $characters[$sum % 47];
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('full_ascii', false);
        $resolver->setAllowedTypes('full_ascii', ['bool']);
    }
}
