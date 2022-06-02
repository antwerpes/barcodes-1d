<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\EAN;

use Illuminate\Support\Str;

/**
 * @see https://en.wikipedia.org/wiki/EAN-2
 */
class EAN2 extends EAN
{
    /** @var string[] */
    protected const STRUCTURE = ['LL', 'LG', 'GL', 'GG'];

    /** @var string */
    protected const START_BITS = '1011';

    /** @var string */
    protected const SEPARATOR = '01';

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return Str::of($this->code)->test('/^[0-9]{2}$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $structure = self::STRUCTURE[(int) $this->code % 4];
        $data = self::START_BITS.$this->encodeData($this->code, $structure, self::SEPARATOR);

        return [
            $this->createEncoding(['data' => $data, 'text' => $this->code]),
        ];
    }
}
