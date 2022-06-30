<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\MSI;

class MSITest extends EncodingTestCase
{
    /** @var string */
    protected const MSI = '1101101001001001001001001001001101001101001001101001001';

    public function test_is_encoded_correctly(): void
    {
        $encoder = new MSI('8052');
        $this->assertSame(self::MSI, $this->getBinaryString($encoder->encode()));
        $this->assertTrue($encoder->isValid());
        $this->assertFalse((new MSI('12A345'))->isValid());
    }

    public function test_mod_10(): void
    {
        $encoder = new MSI('8052', ['check_digit' => MSI::MOD_10]);
        $this->assertSame('80523', $encoder->encode()[0]->text);
    }

    public function test_mod_11(): void
    {
        $encoder = new MSI('8052', ['check_digit' => MSI::MOD_11]);
        $this->assertSame('80527', $encoder->encode()[0]->text);
    }

    public function test_mod_1010(): void
    {
        $encoder = new MSI('8052', ['check_digit' => MSI::MOD_1010]);
        $this->assertSame('805234', $encoder->encode()[0]->text);
    }

    public function test_mod_1110(): void
    {
        $encoder = new MSI('8052', ['check_digit' => MSI::MOD_1110]);
        $this->assertSame('805275', $encoder->encode()[0]->text);
    }
}
