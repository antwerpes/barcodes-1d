<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Code25;

class Code25Test extends EncodingTestCase
{
    /** @var string */
    protected const CODE_25 = '111011101011101010101110101110101011101110111010101010101110101110111010111010101011101110101010101011101110111010111';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Code25('1234567');
        $this->assertSame(self::CODE_25, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Code25('12A345'))->isValid());
    }
}
