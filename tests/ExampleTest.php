<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_it(): void
    {
        $result = Barcodes::create('A123456A', Barcodes\Common\Format::CODABAR)->toSVG();
        file_put_contents('img.svg', $result);
    }
}
