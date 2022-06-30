<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Code25Interleaved;

class Code25InterleavedTest extends EncodingTestCase
{
    /** @var string */
    protected const CODE_25_INTERLEAVED = '101011101000101011100011101110100010100011101000111000101010101000111000111011101';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Code25Interleaved('12345670');
        $this->assertSame(self::CODE_25_INTERLEAVED, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Code25Interleaved('12A345'))->isValid());
    }
}
