<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes;
use Antwerpes\Barcodes\Barcodes\Common\Format;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_it(): void
    {
        $result = Barcodes::create('1234567890', Format::CODE_25)->toSVG();
        file_put_contents('img.svg', $result);
    }
}
