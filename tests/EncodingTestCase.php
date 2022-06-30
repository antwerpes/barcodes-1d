<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use PHPUnit\Framework\TestCase;

abstract class EncodingTestCase extends TestCase
{
    protected function getBinaryString(array $encodings): string
    {
        return trim(collect($encodings)->pluck('data')->join(''), '0');
    }
}
