<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes;
use Antwerpes\Barcodes\Barcodes\Common\Format;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_it(): void
    {
        $result = Barcodes::create('9890364819032', Format::EAN_13, [
            'font_size' => 14,
        ])->toPNG(6);
        file_put_contents('img.png', base64_decode($result, true));
    }
}
