<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Renderers;

use Antwerpes\Barcodes\DTOs\Encoding;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class ImageRenderer extends AbstractRenderer
{
    /** @var int */
    protected const MAGIC_TEXT_MARGIN = 4;

    /**
     * Render the encodings into a base64-encoded image string.
     */
    public function render(): string
    {
        $manager = new ImageManager;
        $image = $manager->canvas(
            $this->getTotalWidth() * $this->options->image_scale,
            $this->getMaxHeight() * $this->options->image_scale,
            $this->options->background,
        );
        $currentX = $this->options->margin_left * $this->options->image_scale;

        foreach ($this->encodings as $encoding) {
            $this->drawBarcode($image, $encoding, $currentX);
            $this->drawText($image, $encoding, $currentX);
            $currentX += (int) ceil($encoding->totalWidth * $this->options->image_scale);
        }

        return base64_encode($image->encode($this->options->image_format)->getEncoded());
    }

    /**
     * Draw barcode for the given $encoding.
     */
    protected function drawBarcode(Image $image, Encoding $encoding, int $currentX): void
    {
        $chunks = $this->getEncodingChunks($encoding->data);

        foreach ($chunks as $chunk) {
            $x = $currentX + ($chunk->first() * $this->options->width * $this->options->image_scale);
            $image->rectangle(
                $x,
                $this->options->margin_top * $this->options->image_scale,
                $x + ($this->options->width * $chunk->count() * $this->options->image_scale) - 1,
                ($this->options->margin_top + $encoding->height) * $this->options->image_scale,
                fn (AbstractShape $shape) => $shape->background($this->options->color),
            );
        }
    }

    /**
     * Draw text for the given $encoding.
     */
    protected function drawText(Image $image, Encoding $encoding, int $currentX): void
    {
        if (! $this->options->display_value || $encoding->text === null) {
            return;
        }

        $position = $currentX + ($this->getTextStart($encoding) * $this->options->image_scale);
        $image->text(
            $encoding->text,
            $position,
            ($this->options->margin_top + $encoding->height + $this->options->text_margin + self::MAGIC_TEXT_MARGIN) * $this->options->image_scale,
            fn (AbstractFont $font) => $font
                ->file($this->options->image_font)
                ->size($this->options->font_size * $this->options->image_scale)
                ->align($encoding->align)
                ->valign('top')
                ->color($this->options->text_color),
        );
    }
}
