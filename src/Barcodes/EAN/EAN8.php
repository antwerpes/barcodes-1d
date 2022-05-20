<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Antwerpes\Barcodes\DTOs\Encoding;
use Illuminate\Support\Str;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see https://en.wikipedia.org/wiki/International_Article_Number
 */
class EAN8 extends EAN
{
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

        if ($this->options['with_quiet_zone']) {
            $encoding = [...$encoding, new Encoding(data: '00000', text: '<'), new Encoding(data: '00')];
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
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 ? 1 : 3)),
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
