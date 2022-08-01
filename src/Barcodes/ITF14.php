<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Antwerpes\Barcodes\Helpers\RegexHelper;

class ITF14 extends Code25Interleaved
{
    public function __construct(string $code, array $options = [])
    {
        if (mb_strlen($code) === 13) {
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
            RegexHelper::test($this->code, '/^[0-9]{14}$/')
            && ((int) $this->code[13]) === $this->calculateChecksum($this->code);
    }

    /**
     * Calculate the checksum.
     */
    protected function calculateChecksum(string $code): int
    {
        $result = collect(mb_str_split(mb_substr($code, 0, 13)))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit * ($idx % 2 === 0 ? 3 : 1)),
            0,
        );

        return (10 - ($result % 10)) % 10;
    }
}
