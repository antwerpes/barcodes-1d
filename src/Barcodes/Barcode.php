<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Barcode
{
    protected string $code;
    protected array $options;

    public function __construct(string $code, array $options = [])
    {
        $this->code = $code;
        $resolver = new OptionsResolver;
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Verify that the given $code is valid.
     */
    abstract public function isValid(): bool;

    /**
     * Encode the given $code in binary form.
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
        ]);
        $resolver->setDefined(['margin_top', 'margin_right', 'margin_bottom', 'margin_left']);
        $resolver->setAllowedTypes('width', ['int']);
        $resolver->setAllowedTypes('height', ['int']);
        $resolver->setAllowedTypes('text_margin', ['int']);
        $resolver->setAllowedTypes('background', ['string', 'null']);
        $resolver->setAllowedTypes('color', ['string']);
        $resolver->setAllowedTypes('margin', ['int']);
        $resolver->setAllowedTypes('display_value', ['bool']);
        $resolver->setAllowedTypes('margin_top', ['int']);
        $resolver->setAllowedTypes('margin_right', ['int']);
        $resolver->setAllowedTypes('margin_bottom', ['int']);
        $resolver->setAllowedTypes('margin_left', ['int']);
        $resolver->setAllowedTypes('text_align', ['string']);
        $resolver->setAllowedValues('text_align', ['left', 'center', 'right']);

        foreach (['margin_top', 'margin_right', 'margin_bottom', 'margin_left'] as $margin) {
            $resolver->setDefault($margin, fn (Options $options) => $options['margin']);
        }
    }
}
