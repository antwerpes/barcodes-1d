<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Illuminate\Support\Str;

/**
 * @see https://en.wikipedia.org/wiki/International_Article_Number
 */
class EAN8 extends EAN
{
    use HasGuardedEncoding;

    public function __construct(string $code, array $options = [])
    {
        if (mb_strlen($code) === 7) {
            $code .= $this->calculateChecksum($code);
        }

        parent::__construct($code, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return
            Str::of($this->code)->test('/^[0-9]{8}$/')
            && ((int) $this->code[7]) === $this->calculateChecksum($this->code);
    }

    /**
     * Encode as the traditional representation with long guard bars.
     */
    protected function encodeGuarded(): array
    {
        $encodings = [];

        if ($this->options['with_quiet_zone']) {
            $encodings = [...$encodings, ...$this->createStartQuietZone()];
        }

        $encodings = [...$encodings, ...$this->createGuardedEncoding()];

        if ($this->options['with_quiet_zone']) {
            $encodings = [...$encodings, ...$this->createEndQuietZone()];
        }

        return $encodings;
    }

    protected function leftText(): string
    {
        return mb_substr($this->code, 0, 4);
    }

    protected function rightText(): string
    {
        return mb_substr($this->code, 4, 4);
    }

    protected function leftEncode(): string
    {
        return $this->encodeData($this->leftText(), 'LLLL');
    }

    protected function rightEncode(): string
    {
        return $this->encodeData($this->rightText(), 'RRRR');
    }

    /**
     * Calculate the checksum for getting the correct structure. See
     * https://en.wikipedia.org/wiki/International_Article_Number for algorithm details.
     */
    protected function calculateChecksum(string $code): int
    {
        $result = collect(mb_str_split(mb_substr($code, 0, 7)))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 !== 0 ? 1 : 3)),
            0,
        );

        return (10 - ($result % 10)) % 10;
    }
}
