<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Pharmacode;

class PharmacodeTest extends EncodingTestCase
{
    /** @var string */
    protected const PHARMACODE = '10010011100111001001110010010011100111';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new Pharmacode('1234');
        $this->assertSame(self::PHARMACODE, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new Pharmacode('12345678'))->isValid());
    }
}
