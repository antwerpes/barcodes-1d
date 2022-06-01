<?php declare(strict_types=1);

namespace Antwerpes\Barcodes;

use Antwerpes\Barcodes\Barcodes\Barcode;
use Antwerpes\Barcodes\Barcodes\Codabar;
use Antwerpes\Barcodes\Barcodes\Common\Format;
use Antwerpes\Barcodes\Barcodes\EAN\EAN13;
use Antwerpes\Barcodes\Barcodes\EAN\EAN2;
use Antwerpes\Barcodes\Barcodes\EAN\EAN5;
use Antwerpes\Barcodes\Barcodes\EAN\EAN8;
use Antwerpes\Barcodes\Barcodes\MSI;
use Antwerpes\Barcodes\Barcodes\Pharmacode;
use Antwerpes\Barcodes\DTOs\BarcodeGlobalOptions;
use Antwerpes\Barcodes\DTOs\Encoding;
use Antwerpes\Barcodes\Exceptions\InvalidCodeException;
use Antwerpes\Barcodes\Renderers\PNGRenderer;
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
        Format::PHARMACODE => Pharmacode::class,
        Format::MSI => MSI::class,
        Format::CODABAR => Codabar::class,
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
     * Render the encoding to PNG, using the given $scale to determine output size.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function toPNG(int|float $scale = 1): string
    {
        $renderer = new PNGRenderer($this->encodings, $this->options, $scale, $this->svg);

        return $renderer->setScale($scale)->render();
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