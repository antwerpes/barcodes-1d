<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\Helpers\RegexHelper;

class UPCA extends EAN
{
    use CalculatesUPCAChecksum;
    use HasGuardedEncoding;

    public function __construct(string $code, array $options = [])
    {
        if (mb_strlen($code) === 11) {
            $code .= $this->calculateUPCAChecksum($code);
        }

        parent::__construct($code, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return
            RegexHelper::test($this->code, '/^[0-9]{12}$/')
            && ((int) $this->code[11]) === $this->calculateUPCAChecksum($this->code);
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
                'text' => $this->code[11],
                'align' => 'right',
            ]);
        }

        return $encodings;
    }

    /**
     * Get text left to the middle guard.
     */
    protected function leftText(): string
    {
        return mb_substr($this->code, 1, 5);
    }

    /**
     * Get text right to the middle guard.
     */
    protected function rightText(): string
    {
        return mb_substr($this->code, 6, 5);
    }

    /**
     * Encode barcode area left to the middle guard.
     */
    protected function leftEncode(): string
    {
        return $this->encodeData($this->leftText(), 'LLLLL');
    }

    /**
     * Encode barcode area right to the middle guard.
     */
    protected function rightEncode(): string
    {
        return $this->encodeData($this->rightText(), 'RRRRR');
    }

    /**
     * Encode the left guard (including the first digit).
     */
    protected function encodeLeftGuard(): string
    {
        return Encodings::SIDE_GUARD.$this->encodeData($this->code[0], 'L');
    }

    /**
     * Encode the right guard (including the last digit).
     */
    protected function encodeRightGuard(): string
    {
        return $this->encodeData($this->code[11], 'R').Encodings::SIDE_GUARD;
    }
}
