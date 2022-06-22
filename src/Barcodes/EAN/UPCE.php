<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\Barcodes\Common\HasGuardedEncoding;
use Illuminate\Support\Str;

class UPCE extends EAN
{
    use CalculatesUPCAChecksum;
    use HasGuardedEncoding;

    /** @var string */
    final public const END_GUARD = '010101';

    /** @var string[] */
    protected const EXPANSIONS = [
        'XX00000XXX',
        'XX10000XXX',
        'XX20000XXX',
        'XXX00000XX',
        'XXXX00000X',
        'XXXXX00005',
        'XXXXX00006',
        'XXXXX00007',
        'XXXXX00008',
        'XXXXX00009',
    ];

    /** @var string[][] */
    protected const PARITIES = [
        ['EEEOOO', 'OOOEEE'],
        ['EEOEOO', 'OOEOEE'],
        ['EEOOEO', 'OOEEOE'],
        ['EEOOOE', 'OOEEEO'],
        ['EOEEOO', 'OEOOEE'],
        ['EOOEEO', 'OEEOOE'],
        ['EOOOEE', 'OEEEOO'],
        ['EOEOEO', 'OEOEOE'],
        ['EOEOOE', 'OEOEEO'],
        ['EOOEOE', 'OEEOEO'],
    ];
    protected string $upcA;

    public function __construct(string $code, array $options = [])
    {
        // For 8-digit codes, we know the number system and checksum. Expand to UPC-A to verify
        if (mb_strlen($code) === 8) {
            $this->upcA = $this->expand(mb_substr($code, 1, 6), (int) $code[0]);
        }

        // For 6-digit codes, assume number system 0 and expand to UPC-A to calculate checksum
        if (mb_strlen($code) === 6) {
            $this->upcA = $this->expand($code);
            $code = $this->upcA[0].$code.$this->upcA[11];
        }

        // For 11-digit codes, calculate UPC-A checksum
        if (mb_strlen($code) === 11) {
            $code .= $this->calculateUPCAChecksum($code);
        }

        // For 12-digit codes, compress UPC-A to UPC-E
        if (mb_strlen($code) === 12 && ($pattern = $this->match(mb_substr($code, 1, 10)))) {
            $this->upcA = $code;
            $code = $this->compress($code, $pattern);
        }

        parent::__construct($code, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return Str::of($this->code)->test('/^[01][0-9]{7}$/') && ($this->code[7] === $this->upcA[11]);
    }

    /**
     * Encode as the traditional representation with long guard bars.
     */
    protected function encodeGuarded(): array
    {
        $encodings = [];

        if ($this->options['display_value']) {
            $encodings[] = $this->createEncoding([
                'data' => '00000000',
                'text' => $this->code[0],
                'align' => 'left',
            ]);
        }

        $encodings = [...$encodings, ...$this->createGuardedEncoding()];

        if ($this->options['display_value']) {
            $encodings[] = $this->createEncoding([
                'data' => '00000000',
                'text' => $this->code[7],
                'align' => 'right',
            ]);
        }

        return $encodings;
    }

    /**
     * Get text between start and end guard.
     */
    protected function middleText(): string
    {
        return mb_substr($this->code, 1, 6);
    }

    /**
     * Encode barcode area between start and end guard.
     */
    protected function middleEncode(): string
    {
        $numberSystem = (int) $this->code[0];
        $checkDigit = (int) $this->code[7];

        return $this->encodeData($this->middleText(), self::PARITIES[$checkDigit][$numberSystem]);
    }

    protected function hasMiddleGuard(): bool
    {
        return false;
    }

    /**
     * Encode the right guard.
     */
    protected function encodeRightGuard(): string
    {
        return self::END_GUARD;
    }

    /**
     * Attempt to match the given UPC-A code against one of the expansion patterns.
     */
    protected function match(string $code): ?string
    {
        foreach (self::EXPANSIONS as $pattern) {
            if (preg_match('/'.str_replace('X', '\d', $pattern).'/', $code)) {
                return $pattern;
            }
        }

        return null;
    }

    /**
     * Expand 6-digit UPC-E code into 12-digit UPC-A code using the given number system.
     */
    protected function expand(string $code, int $numberSystem = 0): string
    {
        // Last digit of 6-digit UPC-E code determines the expansion pattern
        $pattern = self::EXPANSIONS[(int) $code[5]];
        $processedDigits = 0;

        $code = collect(mb_str_split($pattern))->reduce(function (string $carry, string $digit) use (
            $code,
            &$processedDigits
        ) {
            return $carry.($digit !== 'X' ? $digit : $code[$processedDigits++]);
        }, '');
        $result = $numberSystem.$code;

        return $result.$this->calculateUPCAChecksum($result);
    }

    /**
     * Compress 12-digit UPC-A into 8-digit UPC-E using the given $pattern.
     */
    protected function compress(string $code, string $pattern): string
    {
        $result = collect(mb_str_split(mb_substr($code, 1, 10)))->reduce(
            fn (string $carry, string $digit, int $idx) => $carry.($pattern[$idx] === 'X' ? $digit : ''),
            '',
        );

        // number system + compressed 5-digit code + last digit depending on pattern + checksum
        return $code[0].$result.array_search($pattern, self::EXPANSIONS, true).$code[11];
    }
}
