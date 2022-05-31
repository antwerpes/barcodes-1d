<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Barcodes\Common;

use Antwerpes\Barcodes\Barcodes\EAN\Encodings;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait HasGuardedEncoding
{
    /**
     * {@inheritDoc}
     */
    public function encode(): array
    {
        return $this->options['flat'] === true
            ? $this->encodeFlat()
            : $this->encodeGuarded();
    }

    /**
     * Encode as a flat representation (without the long guard bars).
     */
    protected function encodeFlat(): array
    {
        $data = [
            Encodings::SIDE_GUARD,
            $this->leftEncode(),
            Encodings::MIDDLE_GUARD,
            $this->rightEncode(),
            Encodings::SIDE_GUARD,
        ];

        return [
            $this->createEncoding(['data' => implode('', $data), 'text' => $this->code]),
        ];
    }

    protected function createStartQuietZone(): array
    {
        return [
            $this->createEncoding(['data' => '00000', 'text' => '<']),
            $this->createEncoding(['data' => '00']),
        ];
    }

    protected function createEndQuietZone(): array
    {
        return [
            $this->createEncoding(['data' => '00']),
            $this->createEncoding(['data' => '00000', 'text' => '>']),
        ];
    }

    protected function createGuardedEncoding(): array
    {
        $guardHeight = $this->options['height'] + $this->options['text_margin'] + 10;

        return [
            $this->createEncoding(['data' => Encodings::SIDE_GUARD, 'height' => $guardHeight]),
            $this->createEncoding(['data' => $this->leftEncode(), 'text' => $this->leftText()]),
            $this->createEncoding(['data' => Encodings::MIDDLE_GUARD, 'height' => $guardHeight]),
            $this->createEncoding(['data' => $this->rightEncode(), 'text' => $this->rightText()]),
            $this->createEncoding(['data' => Encodings::SIDE_GUARD, 'height' => $guardHeight]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('flat', false);
        $resolver->setDefault('with_quiet_zone', false);
        $resolver->setAllowedTypes('flat', ['bool']);
        $resolver->setAllowedTypes('with_quiet_zone', ['bool']);
    }
}
