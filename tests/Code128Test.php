<?php declare(strict_types=1);

namespace Antwerpes\Barcodes\Tests;

use Antwerpes\Barcodes\Barcodes\Code128;
use ReflectionClass;

class Code128Test extends EncodingTest
{
    protected array $characters;
    protected array $charsetA;
    protected array $charsetB;
    protected array $charsetC;

    public function test_mode_a(): void
    {
        $encoder = new Code128('A12345', ['mode' => Code128::MODE_A]);
        $expected = $this->getExpected([
            $this->getEncoding('START A', $this->charsetA),
            $this->getEncoding('A', $this->charsetA),
            $this->getEncoding('1', $this->charsetA),
            $this->getEncoding('2', $this->charsetA),
            $this->getEncoding('3', $this->charsetA),
            $this->getEncoding('4', $this->charsetA),
            $this->getEncoding('5', $this->charsetA),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    public function test_mode_b(): void
    {
        $encoder = new Code128('A12345', ['mode' => Code128::MODE_B]);
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('1', $this->charsetB),
            $this->getEncoding('2', $this->charsetB),
            $this->getEncoding('3', $this->charsetB),
            $this->getEncoding('4', $this->charsetB),
            $this->getEncoding('5', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    public function test_mode_c(): void
    {
        $encoder = new Code128('123456', ['mode' => Code128::MODE_C]);
        $expected = $this->getExpected([
            $this->getEncoding('START C', $this->charsetA),
            $this->getEncoding('12', $this->charsetC),
            $this->getEncoding('34', $this->charsetC),
            $this->getEncoding('56', $this->charsetC),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    public function test_validation(): void
    {
        $this->assertTrue((new Code128(chr(0x00).'AA12345', ['mode' => Code128::MODE_A]))->isValid());
        $this->assertFalse((new Code128('Aa12345', ['mode' => Code128::MODE_A]))->isValid());
        $this->assertTrue((new Code128('Aa12345', ['mode' => Code128::MODE_B]))->isValid());
        $this->assertFalse((new Code128(chr(0x00).'a12345', ['mode' => Code128::MODE_B]))->isValid());
        $this->assertTrue((new Code128('123456', ['mode' => Code128::MODE_C]))->isValid());
        $this->assertFalse((new Code128('12345', ['mode' => Code128::MODE_C]))->isValid());
        $this->assertTrue((new Code128(chr(0x00).'Aa12345'))->isValid());
    }

    /**
     * # A12345
     * START-B > A > CODE-C > 12 > 34 > CODE-B > 5 (7)
     * START-B > A > 1 > CODE-C > 23 > 45 (6) <---
     * START-B > A > 1 > 2 > 3 > 4 > 5 (7).
     */
    public function test_auto_mode_scenario_1(): void
    {
        $encoder = new Code128('A12345');
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('1', $this->charsetB),
            $this->getEncoding('CODE C', $this->charsetB),
            $this->getEncoding('23', $this->charsetC),
            $this->getEncoding('45', $this->charsetC),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # 12345A
     * START-B > 1 > CODE-C > 23 > 45 > CODE-B > A (7)
     * START-C > 12 > 34 > CODE-B > 5 > A (6) <---
     * START-B > 1 > 2 > 3 > 4 > 5 > A (7).
     */
    public function test_auto_mode_scenario_2(): void
    {
        $encoder = new Code128('12345A');
        $expected = $this->getExpected([
            $this->getEncoding('START C', $this->charsetA),
            $this->getEncoding('12', $this->charsetC),
            $this->getEncoding('34', $this->charsetC),
            $this->getEncoding('CODE B', $this->charsetC),
            $this->getEncoding('5', $this->charsetB),
            $this->getEncoding('A', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # A12345AA
     * START-B > A > 1 > 2 > 3 > 4 > 5 > A (8) <--- (simpler)
     * START-B > A > CODE-C > 12 > 34 > CODE-B > 5 > A (8)
     * START-B > A > 1 > CODE-C > 23 > 45 > CODE-B > A (8).
     */
    public function test_auto_mode_scenario_3(): void
    {
        $encoder = new Code128('A12345A');
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('1', $this->charsetB),
            $this->getEncoding('2', $this->charsetB),
            $this->getEncoding('3', $this->charsetB),
            $this->getEncoding('4', $this->charsetB),
            $this->getEncoding('5', $this->charsetB),
            $this->getEncoding('A', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # A123456A
     * START-B > A > 1 > 2 > 3 > 4 > 5 > 6 > A (9)
     * START-B > A > CODE-C > 12 > 34 > 56 > CODE-B > A (8) <---.
     */
    public function test_auto_mode_scenario_4(): void
    {
        $encoder = new Code128('A123456A');
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('CODE C', $this->charsetB),
            $this->getEncoding('12', $this->charsetC),
            $this->getEncoding('34', $this->charsetC),
            $this->getEncoding('56', $this->charsetC),
            $this->getEncoding('CODE B', $this->charsetC),
            $this->getEncoding('A', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # A1234
     * START-B > A > 1 > 2 > 3 > 4 (6)
     * START-B > A > CODE-C > 12 > 34 (5) <---.
     */
    public function test_auto_mode_scenario_5(): void
    {
        $encoder = new Code128('A1234');
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('CODE C', $this->charsetB),
            $this->getEncoding('12', $this->charsetC),
            $this->getEncoding('34', $this->charsetC),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # 1234A
     * START-B > 1 > 2 > 3 > 4 > A (6)
     * START-C > 12 > 34 > CODE-B > A (5).
     */
    public function test_auto_mode_scenario_6(): void
    {
        $encoder = new Code128('1234A');
        $expected = $this->getExpected([
            $this->getEncoding('START C', $this->charsetA),
            $this->getEncoding('12', $this->charsetC),
            $this->getEncoding('34', $this->charsetC),
            $this->getEncoding('CODE B', $this->charsetC),
            $this->getEncoding('A', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # A1234A
     * START-B > A > 1 > 2 > 3 > 4 > A (7) <--- (simpler)
     * START-B > A > CODE-C > 12 > 34 > CODE-B > A (7).
     */
    public function test_auto_mode_scenario_7(): void
    {
        $encoder = new Code128('A1234A');
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('1', $this->charsetB),
            $this->getEncoding('2', $this->charsetB),
            $this->getEncoding('3', $this->charsetB),
            $this->getEncoding('4', $this->charsetB),
            $this->getEncoding('A', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # 12345A67890
     * START-B > 1 > CODE-C > 23 > 45 > CODE-B > A > 6 > CODE-C > 78 > 90 (11)
     * START-B > 1 > CODE-C > 23 > 45 > CODE-B > A > CODE-C > 67 > 89 > CODE-B > 0 (12)
     * START-C > 12 > 34 > CODE-B > 5 > A > 6 > CODE-C > 78 > 90 (10) <---
     * START-C > 12 > 34 > CODE-B > 5 > A > CODE-C > 67 > 89 > CODE-B > 0 (11).
     */
    public function test_auto_mode_scenario_8(): void
    {
        $encoder = new Code128('12345A67890');
        $expected = $this->getExpected([
            $this->getEncoding('START C', $this->charsetA),
            $this->getEncoding('12', $this->charsetC),
            $this->getEncoding('34', $this->charsetC),
            $this->getEncoding('CODE B', $this->charsetC),
            $this->getEncoding('5', $this->charsetB),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('6', $this->charsetB),
            $this->getEncoding('CODE C', $this->charsetB),
            $this->getEncoding('78', $this->charsetC),
            $this->getEncoding('90', $this->charsetC),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    /**
     * # ABC12345A
     * START-B > A > B > C > 1 > 2 > 3 > 4 > 5 > A (10) <--- (simpler)
     * START-B > A > B > C > CODE-C > 12 > 34 > CODE-B > 5 > A (10)
     * START-B > A > B > C > 1 > CODE-C > 23 > 45 > CODE-B > A (10).
     */
    public function test_auto_mode_scenario_9(): void
    {
        $encoder = new Code128('ABC12345A');
        $expected = $this->getExpected([
            $this->getEncoding('START B', $this->charsetA),
            $this->getEncoding('A', $this->charsetB),
            $this->getEncoding('B', $this->charsetB),
            $this->getEncoding('C', $this->charsetB),
            $this->getEncoding('1', $this->charsetB),
            $this->getEncoding('2', $this->charsetB),
            $this->getEncoding('3', $this->charsetB),
            $this->getEncoding('4', $this->charsetB),
            $this->getEncoding('5', $this->charsetB),
            $this->getEncoding('A', $this->charsetB),
        ]);
        $this->assertSame($expected, $this->getBinaryString($encoder->encode()));
    }

    protected function getExpected(array $values): string
    {
        return implode('', array_column($values, 1))
            .$this->calculateChecksum(array_column($values, 0))
            .'1100011101011';
    }

    protected function getEncoding(string|int $char, array $charset): array
    {
        $value = array_search($char, $charset, true);

        return [$value, $this->characters[$value][3]];
    }

    protected function calculateChecksum(array $values): string
    {
        $sum = collect($values)->reduce(
            fn (int $carry, int $value, int $idx) => $carry + $value * ($idx === 0 ? 1 : $idx),
            0,
        );

        return $this->characters[$sum % 103][3];
    }

    protected function setUp(): void
    {
        $reflect = new ReflectionClass(Code128::class);
        $this->characters = $reflect->getReflectionConstant('CHARACTERS')->getValue();
        $this->charsetA = array_column($this->characters, 0);
        $this->charsetB = array_column($this->characters, 1);
        $this->charsetC = array_column($this->characters, 2);
    }
}
