<?php declare(strict_types=1);

namespace Antwerpes\Barcodes;

use Antwerpes\Barcodes\Barcodes\Barcode;
use Antwerpes\Barcodes\Barcodes\Codabar;
use Antwerpes\Barcodes\Barcodes\Code11;
use Antwerpes\Barcodes\Barcodes\Code128;
use Antwerpes\Barcodes\Barcodes\Code25;
use Antwerpes\Barcodes\Barcodes\Code25Interleaved;
use Antwerpes\Barcodes\Barcodes\Code39;
use Antwerpes\Barcodes\Barcodes\Code93;
use Antwerpes\Barcodes\Barcodes\EAN\EAN13;
use Antwerpes\Barcodes\Barcodes\EAN\EAN2;
use Antwerpes\Barcodes\Barcodes\EAN\EAN5;
use Antwerpes\Barcodes\Barcodes\EAN\EAN8;
use Antwerpes\Barcodes\Barcodes\EAN\UPCA;
use Antwerpes\Barcodes\Barcodes\EAN\UPCE;
use Antwerpes\Barcodes\Barcodes\ITF14;
use Antwerpes\Barcodes\Barcodes\MSI;
use Antwerpes\Barcodes\Barcodes\Pharmacode;
use Antwerpes\Barcodes\Enumerators\Format;
use Antwerpes\Barcodes\Enumerators\Output;
use Antwerpes\Barcodes\Exceptions\InvalidCodeException;
use Antwerpes\Barcodes\Renderers\ImageRenderer;
use Antwerpes\Barcodes\Renderers\SVGRenderer;
use InvalidArgumentException;

class Barcodes
{
    /** @var array<string, class-string> */
    protected const ENCODERS = [
        Format::EAN_2 => EAN2::class,
        Format::EAN_5 => EAN5::class,
        Format::EAN_8 => EAN8::class,
        Format::EAN_13 => EAN13::class,
        Format::UPC_A => UPCA::class,
        Format::UPC_E => UPCE::class,
        Format::PHARMACODE => Pharmacode::class,
        Format::MSI => MSI::class,
        Format::CODABAR => Codabar::class,
        Format::CODE_25 => Code25::class,
        Format::CODE_25_INTERLEAVED => Code25Interleaved::class,
        Format::ITF_14 => ITF14::class,
        Format::CODE_39 => Code39::class,
        Format::CODE_93 => Code93::class,
        Format::CODE_11 => Code11::class,
        Format::CODE_128 => Code128::class,
    ];

    /**
     * Create new instance and encode the given $code.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function create(
        string $code,
        string $format = Format::CODE_128,
        string $output = Output::SVG,
        array $options = [],
    ): string {
        if (! array_key_exists($format, self::ENCODERS)) {
            throw new InvalidArgumentException("Format `{$format}` is not supported.");
        }

        /** @var Barcode $encoder */
        $encoder = new (self::ENCODERS[$format])($code, $options);

        if (! $encoder->isValid()) {
            throw new InvalidCodeException("Invalid code `{$code}` for format `{$format}`");
        }

        $encodings = $encoder->encode();
        $options = $encoder->getOptions();

        return match ($output) {
            Output::SVG => (new SVGRenderer($encodings, $options))->render(),
            default => (new ImageRenderer($encodings, $options))->render(),
        };
    }
}
