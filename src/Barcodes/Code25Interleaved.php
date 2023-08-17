<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Antwerpes\Barcodes\Helpers\RegexHelper;

class Code25Interleaved extends Code25
{
    /** @var string */
    protected const START_BITS = '1010';

    /** @var string */
    protected const END_BITS = '11101';

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return RegexHelper::test($this->code, '/^(\d{2})+$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $data = collect(mb_str_split($this->code, 2))
            ->map(fn (string $pair) => $this->encodePair($pair))
            ->join('');

        return [
            $this->createEncoding(['data' => self::START_BITS.$data.self::END_BITS, 'text' => $this->code]),
        ];
    }

    /**
     * Encode number pair (interleaved).
     */
    protected function encodePair(string $pair): string
    {
        $second = self::WIDTHS[$pair[1]];

        return collect(mb_str_split(self::WIDTHS[$pair[0]]))
            ->map(fn (string $first, int $idx) => ($first === '1' ? '111' : '1').($second[$idx] === '1' ? '000' : '0'))
            ->join('');
    }
}
