<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Renderers;

use Antwerpes\Barcodes\DTOs\BarcodeGlobalOptions;
use Antwerpes\Barcodes\DTOs\Encoding;
use Antwerpes\Barcodes\Exceptions\RequirementsNotInstalledException;
use Imagick;
use ImagickException;

class PNGRenderer
{
    /**
     * @param Encoding[] $encodings
     */
    public function __construct(
        protected array $encodings,
        protected BarcodeGlobalOptions $options,
        protected int|float $scale = 1,
        protected ?string $svg = null,
    ) {}

    /**
     * Render the encodings into a base64-encoded PNG image string.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function render(): string
    {
        if (! class_exists(Imagick::class)) {
            throw new RequirementsNotInstalledException('ext-imagick is required to create PNG files.');
        }

        $svg = $this->svg ?? (new SVGRenderer($this->encodings, $this->options))->render();
        $im = new Imagick;
        [$x, $y] = $this->getResolution($im, $svg);
        $im->setResolution($x, $y);
        $im->readImageBlob($svg);
        $im->setImageFormat('png');
        $blob = $im->getImageBlob();
        $im->clear();
        $im->destroy();

        return base64_encode($blob);
    }

    /**
     * Set scale for the final output, e.g. 1x or 2x.
     */
    public function setScale(int|float $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * Get the new image resolution, depending on the configured scale.
     *
     * @throws ImagickException
     */
    protected function getResolution(Imagick $image, string $svg): array
    {
        $image->readImageBlob($svg);
        $currentResolution = $image->getImageResolution();
        $image->removeImage();

        return [$this->scale * $currentResolution['x'], $this->scale * $currentResolution['y']];
    }
}
