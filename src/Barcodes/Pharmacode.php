<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Illuminate\Support\Str;

/**
 * @see https://en.wikipedia.org/wiki/Pharmacode
 */
class Pharmacode extends Barcode
{
    /** @var string */
    protected const THICK_BAR = '111';

    /** @var string */
    protected const THIN_BAR = '1';

    /** @var string */
    protected const SEPARATOR = '01';

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        if (! Str::of($this->code)->test('/^[0-9]{1,6}$/')) {
            return false;
        }

        $number = (int) $this->code;

        return $number >= 3 && $number <= 131070;
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $data = '';
        $number = (int) $this->code;

        do {
            if (($number & 1) === 0) { // Even
                $data = self::THICK_BAR.$data;
                $number = ($number - 2) / 2;
            } else { // Odd
                $data = self::THIN_BAR.$data;
                $number = ($number - 1) / 2;
            }

            if ($number !== 0) {
                $data = self::SEPARATOR.$data;
            }
        } while ($number !== 0);

        return [
            $this->createEncoding(['data' => $data, 'text' => $this->code]),
        ];
    }
}
