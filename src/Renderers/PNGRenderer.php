<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Renderers;

use Antwerpes\Barcodes\DTOs\Encoding;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class PNGRenderer extends AbstractRenderer
{
    protected int|float $scale = 1;

    /**
     * Render the encodings into a base64-encoded PNG image string.
     */
    public function render(): string
    {
        $manager = new ImageManager;
        $image = $manager->canvas(
            $this->getTotalWidth() * $this->scale,
            $this->getMaxHeight() * $this->scale,
            $this->options->background,
        );
        $currentX = $this->options->margin_left;

        foreach ($this->encodings as $encoding) {
            $this->drawBarcode($image, $encoding, $currentX);
            $this->drawText($image, $encoding, $currentX);
            $currentX += (int) ceil($encoding->totalWidth * $this->scale);
        }

        return base64_encode($image->encode('png')->getEncoded());
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
     * Draw barcode for the given $encoding.
     * We only care about the `1` bits (`0`s are empty spaces). We also want to group `1`s together,
     * so that we only need to draw one (wider) rectangle if two `1`s are next to each other.
     */
    protected function drawBarcode(Image $image, Encoding $encoding, int $currentX): void
    {
        $chunks = $this->getEncodingChunks($encoding->data);

        foreach ($chunks as $chunk) {
            $x = $currentX + ($chunk->first() * $this->options->width * $this->scale);
            $image->rectangle(
                $x,
                $this->options->margin_top * $this->scale,
                $x + ($this->options->width * $chunk->count() * $this->scale) - 1,
                ($this->options->margin_top + $encoding->height) * $this->scale,
                fn (AbstractShape $shape) => $shape->background($this->options->color),
            );
        }
    }

    /**
     * Draw text for the given $encoding. The text-anchor attribute defines how the `x`
     * value is used. For example, for text-anchor=middle, the rendered characters are
     * aligned such that the middle of the text string is at the `x` position.
     */
    protected function drawText(Image $image, Encoding $encoding, int $currentX): void
    {
        if (! $this->options->display_value || $encoding->text === null) {
            return;
        }

        $position = $currentX + ($this->getTextStart($encoding) * $this->scale);
        $image->text(
            $encoding->text,
            $position,
            ($this->options->margin_top + $encoding->height + $this->options->text_margin + 4) * $this->scale,
            fn (AbstractFont $font) => $font
                ->file($this->options->font)
                ->size($this->options->font_size * $this->scale)
                ->align($encoding->align)
                ->valign('top')
                ->color($this->options->text_color),
        );
    }
}
