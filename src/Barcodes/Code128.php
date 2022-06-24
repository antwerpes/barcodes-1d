<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

class Code128 extends Barcode
{
    protected const CHARACTERS = [
        [' ', ' ', '00', '11011001100'],
        ['!', '!', '01', '11001101100'],
        ['"', '"', '02', '11001100110'],
        ['#', '#', '03', '10010011000'],
        ['$', '$', '04', '10010001100'],
        ['%', '%', '05', '10001001100'],
        ['&', '&', '06', '10011001000'],
        ["'", "'", '07', '10011000100'],
        ['(', '(', '08', '10001100100'],
        [')', ')', '09', '11001001000'],
        ['*', '*', '10', '11001000100'],
        ['+', '+', '11', '11000100100'],
        [',', ',', '12', '10110011100'],
        ['-', '-', '13', '10011011100'],
        ['.', '.', '14', '10011001110'],
        ['/', '/', '15', '10111001100'],
        ['0', '0', '16', '10011101100'],
        ['1', '1', '17', '10011100110'],
        ['2', '2', '18', '11001110010'],
        ['3', '3', '19', '11001011100'],
        ['4', '4', '20', '11001001110'],
        ['5', '5', '21', '11011100100'],
        ['6', '6', '22', '11001110100'],
        ['7', '7', '23', '11101101110'],
        ['8', '8', '24', '11101001100'],
        ['9', '9', '25', '11100101100'],
        [':', ':', '26', '11100100110'],
        [';', ';', '27', '11101100100'],
        ['<', '<', '28', '11100110100'],
        ['=', '=', '29', '11100110010'],
        ['>', '>', '30', '11011011000'],
        ['?', '?', '31', '11011000110'],
        ['@', '@', '32', '11000110110'],
        ['A', 'A', '33', '10100011000'],
        ['B', 'B', '34', '10001011000'],
        ['C', 'C', '35', '10001000110'],
        ['D', 'D', '36', '10110001000'],
        ['E', 'E', '37', '10001101000'],
        ['F', 'F', '38', '10001100010'],
        ['G', 'G', '39', '11010001000'],
        ['H', 'H', '40', '11000101000'],
        ['I', 'I', '41', '11000100010'],
        ['J', 'J', '42', '10110111000'],
        ['K', 'K', '43', '10110001110'],
        ['L', 'L', '44', '10001101110'],
        ['M', 'M', '45', '10111011000'],
        ['N', 'N', '46', '10111000110'],
        ['O', 'O', '47', '10001110110'],
        ['P', 'P', '48', '11101110110'],
        ['Q', 'Q', '49', '11010001110'],
        ['R', 'R', '50', '11000101110'],
        ['S', 'S', '51', '11011101000'],
        ['T', 'T', '52', '11011100010'],
        ['U', 'U', '53', '11011101110'],
        ['V', 'V', '54', '11101011000'],
        ['W', 'W', '55', '11101000110'],
        ['X', 'X', '56', '11100010110'],
        ['Y', 'Y', '57', '11101101000'],
        ['Z', 'Z', '58', '11101100010'],
        ['[', '[', '59', '11100011010'],
        ['\\', '\\', '60', '11101111010'],
        [']', ']', '61', '11001000010'],
        ['^', '^', '62', '11110001010'],
        ['_', '_', '63', '10100110000'],
        [0x00, '`', '64', '10100001100'],
        [0x01, 'a', '65', '10010110000'],
        [0x02, 'b', '66', '10010000110'],
        [0x03, 'c', '67', '10000101100'],
        [0x04, 'd', '68', '10000100110'],
        [0x05, 'e', '69', '10110010000'],
        [0x06, 'f', '70', '10110000100'],
        [0x07, 'g', '71', '10011010000'],
        [0x08, 'h', '72', '10011000010'],
        [0x09, 'i', '73', '10000110100'],
        [0x0A, 'j', '74', '10000110010'],
        [0x0B, 'k', '75', '11000010010'],
        [0x0C, 'l', '76', '11001010000'],
        [0x0D, 'm', '77', '11110111010'],
        [0x0E, 'n', '78', '11000010100'],
        [0x0F, 'o', '79', '10001111010'],
        [0x10, 'p', '80', '10100111100'],
        [0x11, 'q', '81', '10010111100'],
        [0x12, 'r', '82', '10010011110'],
        [0x13, 's', '83', '10111100100'],
        [0x14, 't', '84', '10011110100'],
        [0x15, 'u', '85', '10011110010'],
        [0x16, 'v', '86', '11110100100'],
        [0x17, 'w', '87', '11110010100'],
        [0x18, 'x', '88', '11110010010'],
        [0x19, 'y', '89', '11011011110'],
        [0x1A, 'z', '90', '11011110110'],
        [0x1B, '{', '91', '11110110110'],
        [0x1C, '|', '92', '10101111000'],
        [0x1D, '}', '93', '10100011110'],
        [0x1E, '~', '94', '10001011110'],
        [0x1F, 0x7F, '95', '10111101000'],
        ['FNC3', 'FNC3', '96', '10111100010'],
        ['FNC2', 'FNC2', '97', '11110101000'],
        ['SHIFT', 'SHIFT', '98', '11110100010'],
        ['CODE C', 'CODE C', '99', '10111011110'],
        ['CODE B', 'FNC4', 'CODE B', '10111101110'],
        ['FNC4', 'CODE A', 'CODE A', '11101011110'],
        ['FNC1', 'FNC1', 'FNC1', '11110101110'],
        ['START A', 'START A', 'START A', '11010000100'],
        ['START B', 'START B', 'START B', '11010010000'],
        ['START C', 'START C', 'START C', '11010011100'],
    ];
    protected ?string $charset = null;
    protected ?array $charsetA = null;
    protected ?array $charsetB = null;
    protected ?array $charsetC = null;

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $code = $this->code;
        $result = [];
        $chars = [];

        for ($position = 0; $position < mb_strlen($code); ++$position) {
            $char = $code[$position];
            $charset = $this->getCurrentCharset();

            if ($this->shouldSwitchCharset($code, $char, $position)) {
                $set = $this->getCharsetToSwitchTo($code, $char, $position);
                $result[] = $charset === null
                    ? array_search('START '.$set, $this->getCharsetA(), true)
                    : array_search('CODE '.$set, $charset, true);
                $chars[] = $charset === null ? 'START '.$set : 'CODE '.$set;
                $this->charset = $set;
            }

            $chars[] = $this->getCharacter($code, $char, $position);
            $result[] = $this->getCharacterValue($code, $char, $position);
        }

//        dd($chars);

        return $result;
    }

    protected function getCharacter(string $code, string $char, int $position): string
    {
        $charset = $this->getCurrentCharset();

        if ($this->usingCharsetC()) {
            return mb_substr($code, $position, 2);
        }

        if (array_search($char, $charset, true)) {
            return $char;
        }

        return ord($char);
    }

    protected function getCharacterValue(string $code, string $char, int &$position): int
    {
        $charset = $this->getCurrentCharset();

        if ($this->usingCharsetC()) {
            $result = array_search(mb_substr($code, $position, 2), $charset, true);
            ++$position; // Advance 2 instead of 1 position

            return $result;
        }

        return array_search($char, $charset, true) ?: array_search(ord($char), $charset, true);
    }

    /**
     * Check if the next $length characters starting from $start are digits.
     */
    protected function isDigitSequence(string $code, int $start, int $length): bool
    {
        if ($start + $length > mb_strlen($code)) {
            return false;
        }

        for ($i = $start; $i < $start + $length; ++$i) {
            if (! is_numeric($code[$i])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the digit sequence starting from $start is odd or even.
     */
    protected function isEvenLengthDigitSequence(string $code, int $start): bool
    {
        preg_match('/(\d+)/', mb_substr($code, $start, -1), $matches);

        return isset($matches[1]) && mb_strlen($matches[1]) % 2 === 0;
    }

    protected function usingCharsetC(): bool
    {
        return $this->charset === 'C';
    }

    protected function getCharsetA(): array
    {
        if ($this->charsetA !== null) {
            return $this->charsetA;
        }

        return $this->charsetA = array_column(self::CHARACTERS, 0);
    }

    protected function getCharsetB(): array
    {
        if ($this->charsetB !== null) {
            return $this->charsetB;
        }

        return $this->charsetB = array_column(self::CHARACTERS, 1);
    }

    protected function getCharsetC(): array
    {
        if ($this->charsetC !== null) {
            return $this->charsetC;
        }

        return $this->charsetC = array_column(self::CHARACTERS, 2);
    }

    protected function getCurrentCharset(): ?array
    {
        return match ($this->charset) {
            'A' => $this->getCharsetA(),
            'B' => $this->getCharsetB(),
            'C' => $this->getCharsetC(),
            default => null,
        };
    }

    protected function characterInSet(string $char, array $charset): bool
    {
        return in_array($char, $charset, true) || in_array(ord($char), $charset, true);
    }

    protected function shouldSwitchCharset(string $code, string $char, int $position): bool
    {
        if ($this->isDigitSequence($code, $position, 4) && ! $this->usingCharsetC()) {
            preg_match('/(\d+)/', mb_substr($code, $position), $matches);
            $sequenceLength = mb_strlen($matches[1]);

            // Code starts with digit sequence
            if ($position === 0) {
                return true; // C
            }

            // Code ends with digit sequence
            if (preg_match('/\d+$/', mb_substr($code, $position))) {
                return $sequenceLength % 2 === 0; // Should stay in A or B until the next position if length is odd;
            }

            return $sequenceLength >= 6; // Inner digit sequences must be at least 6 digits long
        }

        // If next 2 characters are digits, and we're already in C, don't switch
        if ($this->isDigitSequence($code, $position, 2) && $this->usingCharsetC()) {
            return false;
        }

        $charset = $this->getCurrentCharset();

        return $charset === null || ! $this->characterInSet($char, $charset);
    }

    protected function getCharsetToSwitchTo(string $code, string $char, int $position): string
    {
        if ($this->isDigitSequence($code, $position, 4)) {
            return 'C';
        }

        if ($this->characterInSet($char, $this->getCharsetB())) {
            return 'B';
        }

        return 'A';
    }
}
