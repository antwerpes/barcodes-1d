<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\EAN\EAN13;
use Antwerpes\Barcodes\Barcodes\EAN\EAN2;
use Antwerpes\Barcodes\Barcodes\EAN\EAN5;
use Antwerpes\Barcodes\Barcodes\EAN\EAN8;
use Antwerpes\Barcodes\Barcodes\EAN\UPCA;
use Antwerpes\Barcodes\Barcodes\EAN\UPCE;

class EANTest extends EncodingTest
{
    /** @var string */
    protected const EAN_2 = '10110110001010100001';

    /** @var string */
    protected const EAN_5 = '10110111001010010011010011101010001011010110001';

    /** @var string */
    protected const EAN_8 = '1010001011010111101111010110111010101001110111001010001001011100101';

    /** @var string */
    protected const EAN_13 = '10100010110100111011001100100110111101001110101010110011011011001000010101110010011101000100101';

    /** @var string */
    protected const UPC_A = '10100110010010011011110101000110110001010111101010100010010010001110100111010011101001110100101';

    /** @var string */
    protected const UPC_E = '101011001100100110011101011100101110110011001010101';

    public function test_ean_2_is_encoded_correctly(): void
    {
        $encoder = new EAN2('53');
        $this->assertSame(self::EAN_2, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new EAN2('534'))->isValid());
        $this->assertFalse((new EAN2('AB'))->isValid());
    }

    public function test_ean_5_is_encoded_correctly(): void
    {
        $encoder = new EAN5('52495');
        $this->assertSame(self::EAN_5, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new EAN5('524956'))->isValid());
        $this->assertFalse((new EAN5('AB123'))->isValid());
    }

    public function test_ean_8_is_encoded_correctly(): void
    {
        $encoder = new EAN8('96385074');
        $this->assertSame(self::EAN_8, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Checksum is calculated correctly
        $encoder = new EAN8('9638507');
        $this->assertSame(self::EAN_8, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Flat encoding works
        $encoder = new EAN8('96385074', ['flat' => true]);
        $this->assertSame(self::EAN_8, $this->getBinaryString($encoder->encode()));

        // Invalid codes
        $this->assertFalse((new EAN8('96385075'))->isValid());
        $this->assertFalse((new EAN8('963850777'))->isValid());
        $this->assertFalse((new EAN8('963850'))->isValid());
    }

    public function test_ean_13_is_encoded_correctly(): void
    {
        $encoder = new EAN13('5901234123457');
        $this->assertSame(self::EAN_13, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Checksum is calculated correctly
        $encoder = new EAN13('590123412345');
        $this->assertSame(self::EAN_13, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Flat encoding works
        $encoder = new EAN13('5901234123457', ['flat' => true]);
        $this->assertSame(self::EAN_13, $this->getBinaryString($encoder->encode()));

        // Invalid codes
        $this->assertFalse((new EAN13('5901234123458'))->isValid());
        $this->assertFalse((new EAN13('59012341234579'))->isValid());
        $this->assertFalse((new EAN13('59012341234'))->isValid());
    }

    public function test_upc_a_is_encoded_correctly(): void
    {
        $encoder = new UPCA('123456789999');
        $this->assertSame(self::UPC_A, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Checksum is calculated correctly
        $encoder = new UPCA('12345678999');
        $this->assertSame(self::UPC_A, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Flat encoding works
        $encoder = new UPCA('12345678999', ['flat' => true]);
        $this->assertSame(self::UPC_A, $this->getBinaryString($encoder->encode()));

        // Invalid codes
        $this->assertFalse((new UPCA('123456789997'))->isValid());
        $this->assertFalse((new UPCA('1234567899991'))->isValid());
        $this->assertFalse((new UPCA('1234567899'))->isValid());
    }

    public function test_upc_e_is_encoded_correctly(): void
    {
        // 8-digit code
        $encoder = new UPCE('01245714');
        $this->assertSame(self::UPC_E, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // 6-digit code is expanded correctly assuming a 0 number system
        $encoder = new UPCE('124571');
        $this->assertSame(self::UPC_E, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // 11-digit code is compressed correctly and checksum is calculated
        $encoder = new UPCE('01210000457');
        $this->assertSame(self::UPC_E, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // 12-digit code is compressed correctly
        $encoder = new UPCE('012100004574');
        $this->assertSame(self::UPC_E, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());

        // Flat encoding works
        $encoder = new UPCE('01245714', ['flat' => true]);
        $this->assertSame(self::UPC_E, $this->getBinaryString($encoder->encode()));

        // Invalid codes
        $this->assertFalse((new UPCE('01245715'))->isValid());
        $this->assertFalse((new UPCE('0121000045746'))->isValid());
        $this->assertFalse((new UPCE('12457'))->isValid());
    }
}
