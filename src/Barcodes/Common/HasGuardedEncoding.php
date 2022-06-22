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
            $this->encodeLeftGuard(),
            ...$this->hasMiddleGuard()
                ? [$this->leftEncode(), Encodings::MIDDLE_GUARD, $this->rightEncode()]
                : [$this->middleEncode()],
            $this->encodeRightGuard(),
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

    protected function encodeLeftGuard(): string
    {
        return Encodings::SIDE_GUARD;
    }

    protected function encodeRightGuard(): string
    {
        return Encodings::SIDE_GUARD;
    }

    protected function hasMiddleGuard(): bool
    {
        return true;
    }

    protected function createGuardedEncoding(): array
    {
        $guardHeight = $this->options['height'] + $this->options['text_margin'] + 10;

        return [
            $this->createEncoding(['data' => $this->encodeLeftGuard(), 'height' => $guardHeight]),
            ...$this->hasMiddleGuard()
                ? [
                    $this->createEncoding(['data' => $this->leftEncode(), 'text' => $this->leftText()]),
                    $this->createEncoding(['data' => Encodings::MIDDLE_GUARD, 'height' => $guardHeight]),
                    $this->createEncoding(['data' => $this->rightEncode(), 'text' => $this->rightText()]),
                ]
                : [
                    $this->createEncoding(['data' => $this->middleEncode(), 'text' => $this->middleText()]),
                ],
            $this->createEncoding(['data' => $this->encodeRightGuard(), 'height' => $guardHeight]),
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
