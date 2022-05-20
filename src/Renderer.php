<?php declare(strict_types=1);

namespace Antwerpes\Barcodes;

use Antwerpes\Barcodes\DTOs\Encoding;
use SVG\Nodes\Shapes\SVGRect;
use SVG\SVG;

class Renderer
{
    protected SVG $SVG;

    /**
     * @param Encoding[] $encodings
     */
    public function __construct(
        protected array $encodings,
        protected array $options,
    ) {
        $this->SVG = new SVG;
    }

    public function render(): void
    {
        $currentX = $this->options['margin_left'];
    }

    protected function prepareSVG(): void
    {
        foreach ($this->encodings as $encoding) {
            $areaWidth = mb_strlen($encoding->data) * $this->options['width'];
            $encoding->totalWidth = (int) ceil($areaWidth);
            $encoding->totalHeight = $this->getEncodingHeight($encoding);
        }
        $totalWidth = collect($this->encodings)->sum('totalWidth');
        $maxHeight = collect($this->encodings)->max('totalHeight');
        $width = $totalWidth + $this->options['margin_left'] + $this->options['margin_right'];
        $svg = new SVG($width, $maxHeight);
        $document = $svg->getDocument();
        $document
            ->setAttribute('x', '0px')
            ->setAttribute('y', '0px')
            ->setAttribute('viewBox', "0 0 {$width} {$maxHeight}")
            ->setAttribute('xmlns', 'http://www.w3.org/2000/svg')
            ->setAttribute('version', '1.1')
            ->setStyle('transform', 'translate(0,0)');

        if ($this->options['background']) {
            $rect = new SVGRect(0, 0, $width, $maxHeight);
            $rect->setStyle('fill', $this->options['background']);
            $document->addChild($rect);
        }
    }

    protected function getEncodingHeight(Encoding $encoding)
    {
        $height = $encoding->height || $this->options['height'];
        $textHeight = $this->options['display_value'] && $encoding->text ? 20 + $this->options['text_margin'] : 0;

        return $height + $textHeight + $this->options['margin_top'] + $this->options['margin_bottom'];
    }
}
