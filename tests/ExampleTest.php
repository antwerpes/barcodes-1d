<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_it(): void
    {
        $result = Barcodes::create('1234567', Barcodes\Common\Format::MSI, [
            'check_digit' => Barcodes\MSI::MOD_1110,
        ])->toPNG(2);
        file_put_contents('img.png', base64_decode($result, true));
    }
}
