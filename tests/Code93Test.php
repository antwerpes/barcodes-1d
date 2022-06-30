<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Code93;

class Code93Test extends EncodingTestCase
{
    /** @var string */
    protected const CODE_93 = '1010111101101001101100100101101011001101001101000010101010000101011101101001000101010111101';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Code93('TEST93');
        $this->assertSame(self::CODE_93, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Code93('ab12'))->isValid());
    }

    public function test_full_ascii(): void
    {
        $encoder = new Code93('ab12', ['full_ascii' => true]);
        $this->assertSame(
            '1010111101001100101101010001001100101101001001010010001010001001100101101001110101010111101',
            $this->getBinaryString($encoder->encode()),
        );
        $this->assertTrue((new Code93('ab12', ['full_ascii' => true]))->isValid());
    }
}
