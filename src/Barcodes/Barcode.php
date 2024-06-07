<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Antwerpes\Barcodes\DTOs\BarcodeGlobalOptions;
use Antwerpes\Barcodes\DTOs\Encoding;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Barcode
{
    /** @var int */
    final public const FONT_SIZE = 20;
    protected array $options;

    public function __construct(
        protected string $code,
        array $options = [],
    ) {
        $resolver = new OptionsResolver;
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Get resolved options.
     */
    public function getOptions(): BarcodeGlobalOptions
    {
        return new BarcodeGlobalOptions(
            width: $this->options['width'],
            text_margin: $this->options['text_margin'],
            color: $this->options['color'],
            image_font: $this->options['image_font'],
            image_format: $this->options['image_format'],
            image_scale: $this->options['image_scale'],
            font_size: $this->options['font_size'],
            display_value: $this->options['display_value'],
            margin_top: $this->options['margin_top'],
            margin_right: $this->options['margin_right'],
            margin_bottom: $this->options['margin_bottom'],
            margin_left: $this->options['margin_left'],
            background: $this->options['background'] ?? null,
            text_color: $this->options['text_color'] ?? null,
        );
    }

    /**
     * Verify that the given $code is valid.
     */
    abstract public function isValid(): bool;

    /**
     * Encode the given $code in binary form.
     *
     * @return array<int, Encoding>
     */
    abstract public function encode(): array;

    /**
     * Configure the base options that apply to all barcode types.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'width' => 2,
            'height' => 100,
            'text_margin' => 2,
            'text_align' => 'center',
            'background' => '#ffffff',
            'color' => '#000000',
            'margin' => 10,
            'display_value' => true,
            'font_size' => 20,
            'image_font' => __DIR__.'/../fonts/jetbrains-mono/JetBrainsMono-Regular.ttf',
            'image_format' => 'png',
            'image_scale' => 1,
        ]);
        $resolver->setDefined(['margin_top', 'margin_right', 'margin_bottom', 'margin_left', 'text_color']);
        $resolver->setAllowedTypes('width', ['int']);
        $resolver->setAllowedTypes('height', ['int']);
        $resolver->setAllowedTypes('text_margin', ['int']);
        $resolver->setAllowedTypes('background', ['string', 'null']);
        $resolver->setAllowedTypes('color', ['string']);
        $resolver->setAllowedTypes('text_color', ['string']);
        $resolver->setAllowedTypes('margin', ['int']);
        $resolver->setAllowedTypes('display_value', ['bool']);
        $resolver->setAllowedTypes('margin_top', ['int']);
        $resolver->setAllowedTypes('margin_right', ['int']);
        $resolver->setAllowedTypes('margin_bottom', ['int']);
        $resolver->setAllowedTypes('margin_left', ['int']);
        $resolver->setAllowedTypes('text_align', ['string']);
        $resolver->setAllowedTypes('image_font', ['string', 'int']);
        $resolver->setAllowedTypes('image_format', ['string']);
        $resolver->setAllowedTypes('image_scale', ['int']);
        $resolver->setAllowedTypes('font_size', ['int']);
        $resolver->setAllowedValues('text_align', ['left', 'center', 'right']);
        $resolver->setAllowedValues('image_format', ['png', 'jpg', 'jpeg', 'webp']);

        foreach (['margin_top', 'margin_right', 'margin_bottom', 'margin_left'] as $margin) {
            $resolver->setDefault($margin, fn (Options $options) => $options['margin']);
        }

        $resolver->setDefault('text_color', fn (Options $options) => $options['color']);
    }

    /**
     * Calculate encoding data and create new encoding.
     */
    protected function createEncoding(array $data): Encoding
    {
        $encoding = Encoding::create(array_merge([
            'height' => $this->options['height'],
            'align' => $this->options['text_align'],
        ], $data));

        $encoding->totalWidth = (int) ceil(mb_strlen($encoding->data) * $this->options['width']);
        $encoding->totalHeight = $this->getEncodingHeight($encoding);

        return $encoding;
    }

    /**
     * Get encoding height depending on text and margins that have been configured.
     */
    protected function getEncodingHeight(Encoding $encoding): int
    {
        $textHeight = $this->options['display_value'] && $encoding->text
            ? $this->options['font_size'] + $this->options['text_margin']
            : 0;

        return $encoding->height + $textHeight + $this->options['margin_top'] + $this->options['margin_bottom'];
    }
}
