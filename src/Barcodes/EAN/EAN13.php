<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\DTOs\Encoding;
use Illuminate\Support\Str;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see https://en.wikipedia.org/wiki/International_Article_Number
 */
class EAN13 extends EAN
{
    public const STRUCTURE = [
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
            Str::of($this->code)->test('/^[0-9]{13}$/')
            && ((int) $this->code[12]) === $this->calculateChecksum($this->code);
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        return $this->options['flat'] === true
            ? $this->encodeFlat()
            : $this->encodeGuarded();
    }

    /**
     * Encode as a flat representation (without the long guard bars).
     */
    protected function encodeFlat(): array
    {
        $data = [
            Encodings::SIDE_GUARD,
            $this->leftEncode(),
            Encodings::MIDDLE_GUARD,
            $this->rightEncode(),
            Encodings::SIDE_GUARD,
        ];

        return [new Encoding(data: implode('', $data), text: $this->code)];
    }

    /**
     * Encode as the traditional representation with long guard bars.
     */
    protected function encodeGuarded(): array
    {
        $encoding = [];

        if ($this->options['display_value']) {
            $encoding[] = new Encoding(data: '000000000000', text: $this->code[0]);
        }

        $guardHeight = $this->options['height'] + $this->options['text_margin'] + 10;

        $encoding = [
            ...$encoding,
            new Encoding(data: Encodings::SIDE_GUARD, height: $guardHeight),
            new Encoding(data: $this->leftEncode(), text: $this->leftText()),
            new Encoding(data: Encodings::MIDDLE_GUARD, height: $guardHeight),
            new Encoding(data: $this->rightEncode(), text: $this->rightText()),
            new Encoding(data: Encodings::SIDE_GUARD, height: $guardHeight),
        ];

        if ($this->options['with_quiet_zone']) {
            $encoding = [...$encoding, new Encoding(data: '00'), new Encoding(data: '00000', text: '>')];
        }

        return $encoding;
    }

    protected function leftText(): string
    {
        return mb_substr($this->code, 1, 6);
    }

    protected function rightText(): string
    {
        return mb_substr($this->code, 7, 6);
    }

    protected function leftEncode(): string
    {
        $structure = self::STRUCTURE[(int) $this->code[0]];

        return $this->encodeData($this->leftText(), $structure);
    }

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
        $result = collect(mb_str_split($code))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 ? 3 : 1)),
            0,
        );

        return (10 - ($result % 10)) % 10;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('flat', false);
        $resolver->setDefault('with_quiet_zone', false);
        $resolver->setAllowedTypes('flat', ['bool']);
        $resolver->setAllowedTypes('with_quiet_zone', ['bool']);
    }
}
