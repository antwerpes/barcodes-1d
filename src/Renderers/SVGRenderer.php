<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Renderers;

use Antwerpes\Barcodes\DTOs\Encoding;
use SVG\Nodes\Shapes\SVGRect;
use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Texts\SVGText;
use SVG\SVG;

class SVGRenderer extends AbstractRenderer
{
    /**
     * Render the encodings into an SVG string.
     */
    public function render(): string
    {
        $svg = new SVG;
        $this->createDocument($svg);
        $currentX = $this->options->margin_left;

        foreach ($this->encodings as $encoding) {
            $group = $this->createGroup($currentX);
            $this->drawBarcode($group, $encoding);
            $this->drawText($group, $encoding);
            $svg->getDocument()->addChild($group);
            $currentX += $encoding->totalWidth;
        }

        return $svg->toXMLString(false);
    }

    /**
     * Draw barcode for the given $encoding.
     */
    protected function drawBarcode(SVGGroup $group, Encoding $encoding): void
    {
        $chunks = $this->getEncodingChunks($encoding->data);

        foreach ($chunks as $chunk) {
            $group->addChild(new SVGRect(
                $chunk->first() * $this->options->width,
                0,
                $this->options->width * $chunk->count(),
                $encoding->height,
            ));
        }
    }

    /**
     * Draw text for the given $encoding. The text-anchor attribute defines how the `x`
     * value is used. For example, for text-anchor=middle, the rendered characters are
     * aligned such that the middle of the text string is at the `x` position.
     */
    protected function drawText(SVGGroup $group, Encoding $encoding): void
    {
        if (! $this->options->display_value) {
            return;
        }

        $text = new SVGText($encoding->text);
        $text->setStyle('font', $this->options->font_size.'px monospace');
        $text->setStyle('fill', $this->options->text_color);
        $y = $encoding->height + $this->options->text_margin + $this->options->font_size;
        $text->setAttribute('text-anchor', $this->getTextAnchor($encoding));
        $text->setAttribute('x', (string) $this->getTextStart($encoding));
        $text->setAttribute('y', (string) $y);
        $group->addChild($text);
    }

    /**
     * Create new group starting at $currentX.
     */
    protected function createGroup(int $currentX): SVGGroup
    {
        $group = new SVGGroup;
        $group->setAttribute('transform', "translate({$currentX}, {$this->options->margin_top})");
        $group->setStyle('fill', "{$this->options->color};");

        return $group;
    }

    /**
     * Set up SVG document.
     */
    protected function createDocument(SVG $svg): void
    {
        $width = $this->getTotalWidth();
        $height = $this->getMaxHeight();
        $document = $svg->getDocument();
        $document
            ->setWidth($width)
            ->setHeight($height)
            ->setAttribute('x', '0px')
            ->setAttribute('y', '0px')
            ->setAttribute('viewBox', "0 0 {$width} {$height}")
            ->setStyle('transform', 'translate(0,0)');
        $this->createBackgroundWhenRequested($document, $width, $height);
    }

    /**
     * When requested by the user, create background using the configured color.
     */
    protected function createBackgroundWhenRequested(SVGDocumentFragment $document, int $width, int $height): void
    {
        if ($this->options->background === null) {
            return;
        }

        $rect = new SVGRect(0, 0, $width, $height);
        $rect->setStyle('fill', $this->options->background);
        $document->addChild($rect);
    }

    /**
     * Depending on the encoding text alignment, get the correct text-anchor
     * attribute value.
     */
    protected function getTextAnchor(Encoding $encoding): string
    {
        return match ($encoding->align) {
            'left' => 'start',
            'right' => 'end',
            'center' => 'middle',
        };
    }
}
