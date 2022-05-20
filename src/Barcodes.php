<?php declare(strict_types=1);

namespace Antwerpes\Barcodes;

use Antwerpes\Barcodes\Barcodes\Barcode;
use Antwerpes\Barcodes\Barcodes\EAN\EAN13;
use Antwerpes\Barcodes\Barcodes\EAN\EAN2;
use Antwerpes\Barcodes\Barcodes\EAN\EAN5;
use Antwerpes\Barcodes\Barcodes\EAN\EAN8;
use Antwerpes\Barcodes\Barcodes\Format;

class Barcodes
{
    protected const CLASS_MAP = [
        Format::EAN_2 => EAN2::class,
        Format::EAN_5 => EAN5::class,
        Format::EAN_8 => EAN8::class,
        Format::EAN_13 => EAN13::class,
    ];

    public static function create(string $code, string $format, array $options = [])
    {
        /** @var Barcode $encoder */
        $encoder = new (self::CLASS_MAP[$format])($code, $options);

        return $encoder->encode();
    }
}
