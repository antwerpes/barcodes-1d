<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Codabar;

class CodabarTest extends EncodingTestCase
{
    /** @var string */
    protected const CODABAR = '10110010010101011001010100101101100101010101101001011010100101001001011';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Codabar('A12345B');
        $this->assertSame(self::CODABAR, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Codabar('A12345'))->isValid());
        $this->assertFalse((new Codabar('12345B'))->isValid());
        $this->assertFalse((new Codabar('12345'))->isValid());
        $this->assertFalse((new Codabar('A123C45B'))->isValid());
    }

    public function test_start_stop_characters_are_omitted(): void
    {
        $encoder = new Codabar('A12345B');
        $this->assertSame('12345', $encoder->encode()[0]->text);
    }
}
