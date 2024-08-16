<?php

declare(strict_types=1);

namespace ExampleVendor\ExampleExtension\Tests\Unit;

use ExampleVendor\ExampleExtension\Math;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class MathTest extends UnitTestCase
{
    public function testAddIntegers(): void
    {
        $math = new Math();
        $result = $math->add(2, 3);
        self::assertEquals(5, $result);
    }

    public function testAddFloats(): void
    {
        $math = new Math();
        $result = $math->add(2.5, 3.5);
        self::assertEquals(6.0, $result);
    }

    public function testAddIntegerAndFloat(): void
    {
        $math = new Math();
        $result = $math->add(2, 3.5);
        self::assertEquals(5.5, $result);
    }

    public function testAddFloatAndInteger(): void
    {
        $math = new Math();
        $result = $math->add(2.5, 3);
        self::assertEquals(5.5, $result);
    }

    public function testAddNegativeNumbers(): void
    {
        $math = new Math();
        $result = $math->add(-2, -3);
        self::assertEquals(-5, $result);
    }
}
