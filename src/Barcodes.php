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
use Antwerpes\Barcodes\Barcodes\Common\BarcodeFormat;
use Antwerpes\Barcodes\Barcodes\EAN\EAN13;
use Antwerpes\Barcodes\Barcodes\EAN\EAN2;
use Antwerpes\Barcodes\Barcodes\EAN\EAN5;
use Antwerpes\Barcodes\Barcodes\EAN\EAN8;
use Antwerpes\Barcodes\Barcodes\EAN\UPCA;
use Antwerpes\Barcodes\Barcodes\EAN\UPCE;
use Antwerpes\Barcodes\Barcodes\ITF14;
use Antwerpes\Barcodes\Barcodes\MSI;
use Antwerpes\Barcodes\Barcodes\Pharmacode;
use Antwerpes\Barcodes\DTOs\BarcodeGlobalOptions;
use Antwerpes\Barcodes\DTOs\Encoding;
use Antwerpes\Barcodes\Exceptions\InvalidCodeException;
use Antwerpes\Barcodes\Renderers\ImageRenderer;
use Antwerpes\Barcodes\Renderers\SVGRenderer;
use InvalidArgumentException;

class Barcodes
{
    /** @var array<string, class-string> */
    protected const ENCODERS = [
        BarcodeFormat::EAN_2 => EAN2::class,
        BarcodeFormat::EAN_5 => EAN5::class,
        BarcodeFormat::EAN_8 => EAN8::class,
        BarcodeFormat::EAN_13 => EAN13::class,
        BarcodeFormat::UPC_A => UPCA::class,
        BarcodeFormat::UPC_E => UPCE::class,
        BarcodeFormat::PHARMACODE => Pharmacode::class,
        BarcodeFormat::MSI => MSI::class,
        BarcodeFormat::CODABAR => Codabar::class,
        BarcodeFormat::CODE_25 => Code25::class,
        BarcodeFormat::CODE_25_INTERLEAVED => Code25Interleaved::class,
        BarcodeFormat::ITF_14 => ITF14::class,
        BarcodeFormat::CODE_39 => Code39::class,
        BarcodeFormat::CODE_93 => Code93::class,
        BarcodeFormat::CODE_11 => Code11::class,
        BarcodeFormat::CODE_128 => Code128::class,
    ];
    protected ?BarcodeGlobalOptions $options = null;
    protected array $encodings = [];
    protected ?string $svg = null;

    /**
     * Create new instance and encode the given $code.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function create(string $code, string $format, array $options = []): static
    {
        return (new static)->encode($code, $format, $options);
    }

    /**
     * Create binary encoding for the given barcode. The encoding can then be
     * rendered to one of multiple output formats.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function encode(string $code, string $format, array $options = []): static
    {
        if (! array_key_exists($format, self::ENCODERS)) {
            throw new InvalidArgumentException("Format `{$format}` is not supported.");
        }

        /** @var Barcode $encoder */
        $encoder = new (self::ENCODERS[$format])($code, $options);

        if (! $encoder->isValid()) {
            throw new InvalidCodeException("Invalid code `{$code}` for format `{$format}`");
        }

        $this->encodings = $encoder->encode();
        $this->options = $encoder->getOptions();
        $this->svg = null;

        return $this;
    }

    /**
     * Render the encoding to SVG.
     */
    public function toSVG(): string
    {
        $renderer = new SVGRenderer($this->encodings, $this->options);

        return $this->svg = $renderer->render();
    }

    /**
     * Render the encoding to an image.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function toImage(): string
    {
        $renderer = new ImageRenderer($this->encodings, $this->options);

        return $renderer->render();
    }

    /**
     * Get the encoding results.
     *
     * @return Encoding[]
     */
    public function getEncodings(): array
    {
        return $this->encodings;
    }
}
