<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Antwerpes\Barcodes\Helpers\RegexHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MSI extends Barcode
{
    /** @var null */
    final public const NO_CHECK_DIGIT = null;

    /** @var string */
    final public const MOD_10 = 'MOD_10';

    /** @var string */
    final public const MOD_11 = 'MOD_11';

    /** @var string */
    final public const MOD_1010 = 'MOD_1010';

    /** @var string */
    final public const MOD_1110 = 'MOD_1110';

    /** @var string */
    protected const START_BITS = '110';

    /** @var string */
    protected const END_BITS = '1001';

    /** @var string[] */
    protected const BINARIES = [
        '100100100100', '100100100110', '100100110100', '100100110110', '100110100100',
        '100110100110', '100110110100', '100110110110', '110100100100', '110100100110',
    ];

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return RegexHelper::test($this->code, '/^\d+$/');
    }

    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        $code = $this->code;
        $type = $this->options['check_digit'];

        if ($type === self::MOD_10 || $type === self::MOD_1010) {
            $code .= $this->calculateMod10($code);
        }

        if ($type === self::MOD_11 || $type === self::MOD_1110) {
            $code .= $this->calculateMod11($code);
        }

        if ($type === self::MOD_1010 || $type === self::MOD_1110) {
            $code .= $this->calculateMod10($code);
        }

        $encoded = collect(mb_str_split($code))
            ->map(fn (string $value, int $idx) => self::BINARIES[(int) $value])
            ->join('');
        $data = self::START_BITS.$encoded.self::END_BITS;

        return [
            $this->createEncoding(['data' => $data, 'text' => $code]),
        ];
    }

    /**
     * Calculate Mod 10 check digit.
     */
    protected function calculateMod10(string $code): int
    {
        $sum = collect(mb_str_split(strrev($code)))->reduce(function (int $carry, string $digit, int $idx) {
            $digit = (int) $digit;

            if ($idx % 2 === 0) {
                return $carry + array_sum(array_map('intval', mb_str_split((string) ($digit * 2))));
            }

            return $carry + $digit;
        }, 0);

        return (10 - ($sum % 10)) % 10;
    }

    /**
     * Calculate Mod 11 check digit, using IBM weights.
     */
    protected function calculateMod11(string $code): int
    {
        $weights = [2, 3, 4, 5, 6, 7];

        $sum = collect(mb_str_split(strrev($code)))->reduce(
            fn (int $carry, string $digit, int $idx) => $carry + ((int) $digit) * $weights[$idx % count($weights)],
            0,
        );

        return (11 - ($sum % 11)) % 11;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('check_digit', null);
        $resolver->setAllowedTypes('check_digit', ['string', 'null']);
        $resolver->setAllowedValues('check_digit', [
            self::NO_CHECK_DIGIT,
            self::MOD_10,
            self::MOD_11,
            self::MOD_1010,
            self::MOD_1110,
        ]);
    }
}
