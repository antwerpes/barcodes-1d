<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes;
use Antwerpes\Barcodes\Barcodes\Common\Format;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_it(): void
    {
        $result = Barcodes::create('ABC12345A', Format::CODE_128)->toSVG();
        file_put_contents('img.svg', $result);

        // Code 128 Testcases
        /*
         *  # A12345
            START-A > A > CODE-C > 12 > 34 > CODE-A > 5 (7)
            START-A > A > 1 > CODE-C > 23 > 45 (6)
            START-A > A > 1 > 2 > 3 > 4 > 5 (7)

            # 12345A
            START-A > 1 > CODE-C > 23 > 45 > CODE-A > A (7)
            START-C > 12 > 34 > CODE-A > 5 > A (6)
            START-A > 1 > 2 > 3 > 4 > 5 > A (7)

            # A12345A
            START-A > A > 1 > 2 > 3 > 4 > 5 > A (8)
            START-A > A > CODE-C > 12 > 34 > CODE-A > 5 > A (8)
            START-A > A > 1 > CODE-C > 23 > 45 > CODE-A > A (8)

            # A123456A
            START-A > A > 1 > 2 > 3 > 4 > 5 > 6 > A (9)
            START-A > A > CODE-C > 12 > 34 > 56 > CODE-A > A (8)

            # A1234
            START-A > A > 1 > 2 > 3 > 4 (6)
            START-A > A > CODE-C > 12 > 34 (5)

            # 1234A
            START-A > 1 > 2 > 3 > 4 > A (6)
            START-C > 12 > 34 > CODE-A > A (5)

            # A1234A
            START-A > A > 1 > 2 > 3 > 4 > A (7)
            START-A > A > CODE-C > 12 > 34 > CODE-A > A (7)

            # 12345A67890
            START-A > 1 > CODE-C > 23 > 45 > CODE-A > A > 6 > CODE-C > 78 > 90 (11)
            START-A > 1 > CODE-C > 23 > 45 > CODE-A > A > CODE-C > 67 > 89 > CODE-A > 0 (12)
            START-C > 12 > 34 > CODE-A > 5 > A > 6 > CODE-C > 78 > 90 (10)
            START-C > 12 > 34 > CODE-A > 5 > A > CODE-C > 67 > 89 > CODE-A > 0 (11)

            # ABC12345A
            START-A > A > B > C > 1 > 2 > 3 > 4 > 5 > A (10)
            START-A > A > B > C > CODE-C > 12 > 34 > CODE-A > 5 > A (10)
            START-A > A > B > C > 1 > CODE-C > 23 > 45 > CODE-A > A (10)
         */
    }
}
