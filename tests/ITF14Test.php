<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\ITF14;

class ITF14Test extends EncodingTestCase
{
    /** @var string */
    protected const ITF_14 = '101010001110101110001010100010001110111011101011100010100011101110001010100011101010001000111010111000101110100011100010001010111011101';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new ITF14('98765432109213');
        $this->assertSame(self::ITF_14, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new ITF14('12A345'))->isValid());
        $this->assertFalse((new ITF14('987654321092'))->isValid());
        $this->assertFalse((new ITF14('98765432109215'))->isValid());

        // Checksum is calculated correctly
        $encoder = new ITF14('9876543210921');
        $this->assertSame(self::ITF_14, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
    }
}
