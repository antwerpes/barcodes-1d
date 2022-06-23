<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Code39 extends Barcode
{
    /** @var array<int|string, string> */
    protected const BINARIES = [
        '0' => '101000111011101',
        '1' => '111010001010111',
        '2' => '101110001010111',
        '3' => '111011100010101',
        '4' => '101000111010111',
        '5' => '111010001110101',
        '6' => '101110001110101',
        '7' => '101000101110111',
        '8' => '111010001011101',
        '9' => '101110001011101',
        'A' => '111010100010111',
        'B' => '101110100010111',
        'C' => '111011101000101',
        'D' => '101011100010111',
        'E' => '111010111000101',
        'F' => '101110111000101',
        'G' => '101010001110111',
        'H' => '111010100011101',
        'I' => '101110100011101',
        'J' => '101011100011101',
        'K' => '111010101000111',
        'L' => '101110101000111',
        'M' => '111011101010001',
        'N' => '101011101000111',
        'O' => '111010111010001',
        'P' => '101110111010001',
        'Q' => '101010111000111',
        'R' => '111010101110001',
        'S' => '101110101110001',
        'T' => '101011101110001',
        'U' => '111000101010111',
        'V' => '100011101010111',
        'W' => '111000111010101',
        'X' => '100010111010111',
        'Y' => '111000101110101',
        'Z' => '100011101110101',
        '-' => '100010101110111',
        '.' => '111000101011101',
        ' ' => '100011101011101',
        '$' => '100010001000101',
        '/' => '100010001010001',
        '+' => '100010100010001',
        '%' => '101000100010001',
        '*' => '100010111011101',
    ];

    /** @var array<int, string> */
    protected const REPRESENTATIONS = [
        0 => '%U',
        1 => '$A',
        2 => '$B',
        3 => '$C',
        4 => '$D',
        5 => '$E',
        6 => '$F',
        7 => '$G',
        8 => '$H',
        9 => '$I',
        10 => '$J',
        11 => '$K',
        12 => '$L',
        13 => '$M',
        14 => '$N',
        15 => '$O',
        16 => '$P',
        17 => '$Q',
        18 => '$R',
        19 => '$S',
        20 => '$T',
        21 => '$U',
        22 => '$V',
        23 => '$W',
        24 => '$X',
        25 => '$Y',
        26 => '$Z',
        27 => '%A',
        28 => '%B',
        29 => '%C',
        30 => '%D',
        31 => '%E',
        33 => '/A',
        34 => '/B',
        35 => '/C',
        36 => '/D',
        37 => '/E',
        38 => '/F',
        39 => '/G',
        40 => '/H',
        41 => '/I',
        42 => '/J',
        43 => '/K',
        44 => '/L',
        47 => '/O',
        49 => '%F',
        58 => '/Z',
        60 => '%G',
        61 => '%H',
        62 => '%I',
        63 => '%J',
        64 => '%V',
        91 => '%K',
        92 => '%L',
        93 => '%M',
        94 => '%N',
        95 => '%O',
        96 => '%W',
        123 => '%P',
        124 => '%Q',
        125 => '%R',
        126 => '%S',
        127 => '%T',
        97 => '+A',
        98 => '+B',
        99 => '+C',
        100 => '+D',
        101 => '+E',
        102 => '+F',
        103 => '+G',
        104 => '+H',
        105 => '+I',
        106 => '+J',
        107 => '+K',
        108 => '+L',
        109 => '+M',
        110 => '+N',
        111 => '+O',
        112 => '+P',
        113 => '+Q',
        114 => '+R',
        115 => '+S',
        116 => '+T',
        117 => '+U',
        118 => '+V',
        119 => '+W',
        120 => '+X',
        121 => '+Y',
        122 => '+Z',
    ];

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        $code = $this->options['allow_extended'] ? $this->replaceASCIICharacters($this->code) : $this->code;

        return collect(mb_str_split($code))
            ->every(fn (string $value) => array_key_exists($value, self::BINARIES));
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $code = str_replace('*', '', $this->code);

        if ($this->options['allow_extended']) {
            $code = $this->replaceASCIICharacters($code);
        }

        if ($this->options['enable_checksum']) {
            $code .= $this->calculateChecksum($code);
        }

        $code = '*'.$code.'*';

        $data = collect(mb_str_split($code))
            ->map(fn (string $char) => self::BINARIES[$char])
            ->join('0');

        return [
            $this->createEncoding(['data' => $data, 'text' => $this->code]),
        ];
    }

    /**
     * Replace ASCII characters with their 2-character representation.
     */
    protected function replaceASCIICharacters(string $code): string
    {
        return collect(mb_str_split($code))
            ->map(fn (string $char) => self::REPRESENTATIONS[ord($char)] ?? $char)
            ->join('');
    }

    /**
     * Calculate and append the checksum.
     */
    protected function calculateChecksum(string $code): int
    {
        $charset = array_flip(array_keys(self::BINARIES));

        $result = collect(mb_str_split($code))->reduce(
            fn (int $carry, string $char) => $carry + $charset[$char],
            0,
        );

        return $result % 43;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('enable_checksum', false);
        $resolver->setAllowedTypes('enable_checksum', ['bool']);
        $resolver->setDefault('allow_extended', false);
        $resolver->setAllowedTypes('allow_extended', ['bool']);
    }
}
