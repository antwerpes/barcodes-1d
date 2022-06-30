<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Code39;

class Code39Test extends EncodingTestCase
{
    /** @var string */
    protected const CODE_39 = '10001011101110101110101000101110101110100010111011101000101011101011100010101110100010111011101';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Code39('AB12');
        $this->assertSame(self::CODE_39, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Code39('ab12'))->isValid());
    }

    public function test_calculates_checksum(): void
    {
        $encoder = new Code39('AB12', ['enable_checksum' => true]);
        $this->assertSame(
            '1000101110111010111010100010111010111010001011101110100010101110101110001010111010111000101011101010001110101110100010111011101',
            $this->getBinaryString($encoder->encode()),
        );
    }

    public function test_full_ascii(): void
    {
        $encoder = new Code39('ab12', ['full_ascii' => true]);
        $this->assertSame(
            '1000101110111010100010100010001011101010001011101000101000100010101110100010111011101000101011101011100010101110100010111011101',
            $this->getBinaryString($encoder->encode()),
        );
        $this->assertTrue((new Code39('ab12', ['full_ascii' => true]))->isValid());
    }
}
