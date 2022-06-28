<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Code11;

class Code11Test extends EncodingTest
{
    /** @var string */
    protected const CODE_11 = '1011001010101101101011010010110110010101011011010110101101101010011010101001101101001010110110110101101011001';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Code11('01234-5678');
        $this->assertSame(self::CODE_11, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Code11('12A345'))->isValid());
    }
}
