<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\Helpers\RegexHelper;

/**
 * @see https://en.wikipedia.org/wiki/International_Article_Number
 */
class EAN13 extends EAN
{
    use HasGuardedEncoding;

    /** @var string[] */
    protected const STRUCTURE = [
        'LLLLLL', 'LLGLGG', 'LLGGLG', 'LLGGGL', 'LGLLGG',
        'LGGLLG', 'LGGGLL', 'LGLGLG', 'LGLGGL', 'LGGLGL',
    ];

    public function __construct(string $code, array $options = [])
    {
        if (mb_strlen($code) === 12) {
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
            RegexHelper::test($this->code, '/^[0-9]{13}$/')
            && ((int) $this->code[12]) === $this->calculateChecksum($this->code);
    }

    /**
     * Encode as the traditional representation with long guard bars.
     */
    protected function encodeGuarded(): array
    {
        $encodings = [];

        if ($this->options['display_value']) {
            $encodings[] = $this->createEncoding([
                'data' => '000000000000',
                'text' => $this->code[0],
                'align' => 'left',
            ]);
        }

        $encodings = [...$encodings, ...$this->createGuardedEncoding()];

        if ($this->options['with_quiet_zone']) {
            $encodings = [...$encodings, ...$this->createEndQuietZone()];
        }

        return $encodings;
    }

    /**
     * Get text left to the middle guard.
     */
    protected function leftText(): string
    {
        return mb_substr($this->code, 1, 6);
    }

    /**
     * Get text right to the middle guard.
     */
    protected function rightText(): string
    {
        return mb_substr($this->code, 7, 6);
    }

    /**
     * Encode barcode area left to the middle guard.
     */
    protected function leftEncode(): string
    {
        $structure = self::STRUCTURE[(int) $this->code[0]];

        return $this->encodeData($this->leftText(), $structure);
    }

    /**
     * Encode barcode area right to the middle guard.
     */
    protected function rightEncode(): string
    {
        return $this->encodeData($this->rightText(), 'RRRRRR');
    }

    /**
     * Calculate the checksum for getting the correct structure. See
     * https://en.wikipedia.org/wiki/International_Article_Number for algorithm details.
     */
    protected function calculateChecksum(string $code): int
    {
        $result = collect(mb_str_split(mb_substr($code, 0, 12)))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 === 0 ? 1 : 3)),
            0,
        );

        return (10 - ($result % 10)) % 10;
    }
}
